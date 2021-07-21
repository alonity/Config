# Config
Configuration component

## Install

`composer require alonity/config`

### Examples
```php
use alonity\config\Config;

require('vendor/autoload.php');

$mainConfig = Config::get('main'); // ./vendor/../main.php

$value = Config::getValue('main', 'example'); // Like $mainConfig['example']

$load = Config::loader([
    'main',
    'Other' => '../../../../configs/other'
]);
// Now you can call to config via $load['Other'] or Config::get('Other')
```

Documentation: https://alonity.gitbook.io/alonity/components/config