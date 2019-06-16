<?php

use Tracy\Debugger;

require __DIR__ . '/../vendor/autoload.php';

Debugger::enable();

//DIRECTORY_SEPARATOR is PHP constant holding system dir. separator (\ for Win and / for Linux)
define('APP_DIR', __DIR__);
define('DATA_DIR', APP_DIR . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'data');
define('WWW_DIR', APP_DIR . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'www');
define('DATA_PUBLIC_DIR', WWW_DIR . DIRECTORY_SEPARATOR . 'data_public');
define('ASSETS_DIR', WWW_DIR . DIRECTORY_SEPARATOR . 'assets');
define('LOGOS_DIR', DATA_PUBLIC_DIR . DIRECTORY_SEPARATOR . 'logos');
define('TMP_DIR', APP_DIR . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'temp');
define("LOGOS_TMP_DIR", TMP_DIR . DIRECTORY_SEPARATOR . "logos");
define('NPM_DIR', APP_DIR . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'node_modules');
define('VENDOR_DIR', APP_DIR . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'vendor');


$configurator = new Nette\Configurator;

$configurator->setDebugMode(true);
$configurator->enableTracy(__DIR__ . '/../log');

$configurator->setTimeZone('Europe/Prague');
$configurator->setTempDirectory(__DIR__ . '/../temp');

$configurator->createRobotLoader()
	->addDirectory(__DIR__)
    ->addDirectory(__DIR__ . '/../tests')
	->register();

$configurator->addConfig(__DIR__ . '/Config/config.neon');
$configurator->addConfig(__DIR__ . '/Config/config.local.neon');

$configurator->addParameters([
    'assetsDir' => ASSETS_DIR,
    'logosDir' => LOGOS_DIR,
    "logosTmpDir" => LOGOS_TMP_DIR,
    'npmDir' => NPM_DIR,
    'wwwDir' => WWW_DIR,
    'vendorDir' => VENDOR_DIR
]);

$container = $configurator->createContainer();

return $container;
