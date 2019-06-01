<?php
/**
 * Diglin GmbH - Switzerland
 *
 * @author      Sylvain Rayé <support at diglin.com>
 * @category    Diglin
 * @copyright   Copyright (c) Diglin (https://www.diglin.com)
 */

namespace MagentoHackathon\CliPluginList\Console\Command;

use Magento\Developer\Model\Di\PluginList;
use Magento\Framework\App\ObjectManager;
use Magento\Framework\Config\ScopeInterface;
use Magento\Framework\Console\Cli;
use Magento\Setup\Console\Style\MagentoStyle;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\TableSeparator;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class PluginListCommand extends Command
{
    const AREA_GLOBAL = 'global';

    /**
     * @var ScopeInterface
     */
    private $scope;

    /**
     * PluginListCommand constructor.
     *
     * @param ScopeInterface $scope
     * @param string|null $name
     */
    public function __construct(
        ScopeInterface $scope,
        ?string $name = null
    ) {
        $this->scope = $scope;

        parent::__construct($name);
    }

    public function configure()
    {
        $this->setName('hackathon:plugin:list');
        $this->setDescription('Get the list of plugins installed in Magento');
        $this->addArgument(
            'area',
            InputArgument::OPTIONAL,
            'Specify the area to lookup. All non global area will include global plugins. Value examples: global, frontend, adminhtml, webapi_rest, webapi_soap',
            self::AREA_GLOBAL
        );
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     *
     * @return int|null
     * @throws \ReflectionException
     */
    public function execute(InputInterface $input, OutputInterface $output)
    {
        $totalFound = 0;
        $start = microtime(true);

        $style = new MagentoStyle($input, $output);
        $style->writeln('<info>Generating list of plugins for the area ' . $input->getArgument('area') . '...</info>');
        $style->writeln('');

        $classes = $this->getClasses($input, $style);

        if (null === $classes) {
            return Cli::RETURN_FAILURE;
        }

        foreach ($classes as $value) {
            $rows = [];
            $result = [];
            $pluginListDev = ObjectManager::getInstance()->create(PluginList::class);
            $pluginInfos = $pluginListDev->getPluginsListByClass($value);

            foreach ($pluginInfos as $placement => $pluginInfo) {

                if (empty($pluginInfo)) {
                    continue;
                }

                foreach ($pluginInfo as $pluginClass => $methods) {
                    $totalFound += count($methods);
                    $result[$pluginClass][$placement] = implode(PHP_EOL, $methods);
                }
            }

            if (empty($result)) {
                continue;
            }

            $pluginsClasses = array_keys($result);

            foreach ($pluginsClasses as $pluginClass) {
                $rows[] = [
                    $pluginClass,
                    $result[$pluginClass]['before'] ?? '--',
                    $result[$pluginClass]['around'] ?? '--',
                    $result[$pluginClass]['after'] ?? '--',
                ];
            }

            $style->table(
                [
                    $value,
                    'before',
                    'around',
                    'after',
                ],
                $rows
            );
        }

        $end = microtime(true);
        $time = round($end - $start, 2);

        $style->writeln(sprintf('%d plugins found', $totalFound));
        $style->writeln('Memory max usage: ' . (memory_get_peak_usage(true) / (1024 * 1024)) . 'MB');
        $style->writeln(sprintf('Time to generate: %s sec', $time));

        return Cli::RETURN_SUCCESS;
    }

    /**
     * @param InputInterface $input
     *
     * @return null|array
     * @throws \ReflectionException
     */
    protected function getClasses(InputInterface $input, MagentoStyle $style): ?array
    {
        /** @var \Magento\Developer\Model\Di\PluginList $pluginListDev */
        $pluginListDev = ObjectManager::getInstance()->get(PluginList::class);

        try {
            $reflector = new \ReflectionClass($pluginListDev);
        } catch (\ReflectionException $e) {
            $style->error($e->getMessage());

            return null;
        }

        $this->scope->setCurrentScope($input->getArgument('area'));

        $method = $reflector->getMethod('_loadScopedData');
        $method->setAccessible(true);
        $method->invoke($pluginListDev);

        $property = $reflector->getProperty('_data');
        $property->setAccessible(true);
        $data = $property->getValue($pluginListDev);
        ksort($data);

        return array_keys($data);
    }
}
