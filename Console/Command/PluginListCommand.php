<?php
/**
 * Diglin GmbH - Switzerland
 *
 * @author      Sylvain Rayé <support at diglin.com>
 * @category    Diglin
 * @copyright   Copyright (c) Diglin (https://www.diglin.com)
 */

namespace MagentoHackathon\CliPluginList\Console\Command;

use Magento\Framework\Console\Cli;
//use MagentoHackathon\CliPluginList\Console\Style\SymfonyStyle;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class PluginListCommand extends Command
{
    public function configure()
    {
        $this->setName('hackathon:plugin:list');
        $this->setDescription('get the list of plugins installed in Magento');
        $this->addArgument(
            'area',
            InputArgument::OPTIONAL,
            'Specify the area to lookup: e.g. global, frontend, adminhtml, webapi_rest, webapi_soap'
        );
    }

    public function execute(InputInterface $input, OutputInterface $output)
    {
//        $style = new SymfonyStyle($input, $output);

        $output->writeln('<info>Generate list of plugins</info>');

        if (false) {
            $output->writeln(sprintf('<error>No plugin found</error>'));

            return Cli::RETURN_FAILURE;
        }

        //        $rows = [];
        //        foreach ($execution->getStepExecutions() as $stepExecution) {
        //            $rows[] = [
        //                $this->flexData->getBatchJob()->getName(),
        //                $stepExecution->getStepName(),
        //                $stepExecution->getStatus(),
        //                $stepExecution->getReadCount(),
        //                $stepExecution->getWriteCount(),
        //                $stepExecution->getFilterCount(),
        //                $stepExecution->getStartTime()->format('c'),
        //                $stepExecution->getEndTime()->format('c'),
        //            ];
        //        }

        //        $style->table(
        //            [
        //                'Job',
        //                'Step',
        //                'Status',
        //                'Read count',
        //                'Write count',
        //                'Filter count',
        //                'Start time',
        //                'End time',
        //            ],
        //            $rows
        //        );

        $output->writeln('Memory max usage: ' . memory_get_peak_usage(true));
    }
}