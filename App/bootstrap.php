<?php

require __DIR__ . '/../vendor/autoload.php';

// DIRECTORY_SEPARATOR is PHP constant holding system dir. separator (\ for Win and / for Linux)
define('APP_DIR', __DIR__);

define('CORE_MODULE_DIR', APP_DIR . DIRECTORY_SEPARATOR . 'CoreModule' . DIRECTORY_SEPARATOR);
define('CORE_MODULE_TEMPLATES_DIR', CORE_MODULE_DIR . 'templates' . DIRECTORY_SEPARATOR);

define('TEACHER_MODULE_DIR', APP_DIR . DIRECTORY_SEPARATOR . 'TeacherModule' . DIRECTORY_SEPARATOR);
define('TEACHER_MODULE_TEMPLATES_DIR', TEACHER_MODULE_DIR . 'templates' . DIRECTORY_SEPARATOR);
define('TEACHER_TEST_TEMPLATE_DIR', TEACHER_MODULE_TEMPLATES_DIR . 'pdf' . DIRECTORY_SEPARATOR . 'testPdf' . DIRECTORY_SEPARATOR);

define('STUDENT_MODULE_DIR', APP_DIR . DIRECTORY_SEPARATOR . 'StudentModule' . DIRECTORY_SEPARATOR);
define('STUDENT_MODULE_TEMPLATES_DIR', STUDENT_MODULE_DIR . 'templates' . DIRECTORY_SEPARATOR);

define('DATA_DIR', APP_DIR . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'data');
define('TEST_DATA_DIR', DATA_DIR . DIRECTORY_SEPARATOR . 'tests' . DIRECTORY_SEPARATOR);
define('TEST_TEMPLATES_DATA_DIR', DATA_DIR. DIRECTORY_SEPARATOR . 'test_templates' . DIRECTORY_SEPARATOR);

define('WWW_DIR', APP_DIR . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'www');
define('DATA_PUBLIC_DIR', WWW_DIR . DIRECTORY_SEPARATOR . 'data_public');
define('ASSETS_DIR', WWW_DIR . DIRECTORY_SEPARATOR . 'assets');
define('LOGOS_DIR', DATA_PUBLIC_DIR . DIRECTORY_SEPARATOR . 'logos');
define('TMP_DIR', APP_DIR . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'temp');
define('LOGOS_TMP_DIR', TMP_DIR . DIRECTORY_SEPARATOR . 'logos');
define('NPM_DIR', APP_DIR . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'node_modules');
define('VENDOR_DIR', APP_DIR . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'vendor');


$configurator = new Nette\Configurator;

//$configurator->setDebugMode(true);
$configurator->setDebugMode(filter_input(INPUT_SERVER, 'HTTP_EDOMPALLOWDEBUG') === 'SJPPFguvBhl9zN84nviZ');
$configurator->enableTracy(__DIR__ . '/../log');

$configurator->setTimeZone('Europe/Prague');
$configurator->setTempDirectory(__DIR__ . '/../temp');

// Get environment
require __DIR__ . '/environment.php';

// Load configurations based on environment
$configurator->addConfig(__DIR__ . '/Config/config.neon');
$configurator->addConfig(__DIR__ . '/Config/config.local.neon');
//$configurator->addConfig(__DIR__ . '/Config/config.' . ENVIRONMENT . '.neon');

$configurator->addParameters([
    'coreTemplatesDir' => CORE_MODULE_TEMPLATES_DIR,
    'studentTemplatesDir' => STUDENT_MODULE_TEMPLATES_DIR,
    'teacherTemplatesDir' => TEACHER_MODULE_TEMPLATES_DIR,
    'assetsDir' => ASSETS_DIR,
    'logosDir' => LOGOS_DIR,
    'logosTmpDir' => LOGOS_TMP_DIR,
    'npmDir' => NPM_DIR,
    'wwwDir' => WWW_DIR,
    'vendorDir' => VENDOR_DIR
]);

$container = $configurator->createContainer();

return $container;
