# CLI Plugin List for Magento 2

Get a good overview of plugins installed and be aware about their influence on your Magento 2 instance.

# Features list
- Get the list of plugins registered
- Get a list per area: global, frontend, adminhtml, webapi_rest, webapi_soap
- Detect easily in which way a plugin interfere with a method (before, after, around) and nesting

# Installation

`composer require --dev magentohackathon/clipluginlist`

# Usage

`bin/magento hackathon:plugin:list frontend`

You can replace the area `frontend` with `adminhtml`, `webapi_rest`, `webapi_soap`.
By default, `global` is used. In any case, global plugins are always displayed.

![preview](./doc/preview.png)

# License

OSL v3.0

# Author

- Torben Höhn
- Sylvain Rayé
