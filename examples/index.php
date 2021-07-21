<?php

use alonity\config\Config;

ini_set('display_errors', true);
error_reporting(E_ALL);

require_once('../vendor/autoload.php');

$mainConfig = Config::get('main'); // ./vendor/../main.php

$value = Config::getValue('main', 'example'); // Like $mainConfig['example']

$load = Config::loader([
    'main', // Default directory is ../../../../app
    'Other' => '../../../../configs/other'
]);
// Now you can call to config via $load['Other'] or Config::get('Other')

// Set default directory
Config::$path = '../../../../app/configs';

?>