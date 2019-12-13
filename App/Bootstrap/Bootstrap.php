<?php
/**
 * Created by PhpStorm.
 * User: wiedzmin
 * Date: 24.11.19
 * Time: 16:03
 */

namespace App\Bootstrap;


use Nette\Configurator;

/**
 * Class Bootstrap
 * @package App\Bootstrap
 */
final class Bootstrap
{
    public const ENV_PRODUCTION = 'prod';
    public const ENV_DEVELOPMENT = 'devel';

    /**
     * @param Configurator $configurator
     * @return Configurator
     */
    protected static function setConfiguratorParameters(Configurator $configurator): Configurator
    {
        // DIRECTORY_SEPARATOR is PHP constant holding system dir. separator (\ for Win and / for Linux)
        define('APP_DIR', __DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR);

        define('DOCTRINE_DIR', __DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR);

        define('CORE_MODULE_DIR', APP_DIR . 'CoreModule' . DIRECTORY_SEPARATOR);
        define('CORE_MODULE_TEMPLATES_DIR', CORE_MODULE_DIR . 'templates' . DIRECTORY_SEPARATOR);

        define('TEACHER_MODULE_DIR', APP_DIR . 'TeacherModule' . DIRECTORY_SEPARATOR);
        define('TEACHER_MODULE_TEMPLATES_DIR', TEACHER_MODULE_DIR . 'templates' . DIRECTORY_SEPARATOR);
        define('TEACHER_TEST_TEMPLATE_DIR', TEACHER_MODULE_TEMPLATES_DIR . 'pdf' . DIRECTORY_SEPARATOR . 'testPdf' . DIRECTORY_SEPARATOR);

        define('STUDENT_MODULE_DIR', APP_DIR . 'StudentModule' . DIRECTORY_SEPARATOR);
        define('STUDENT_MODULE_TEMPLATES_DIR', STUDENT_MODULE_DIR . 'templates' . DIRECTORY_SEPARATOR);

        define('DATA_DIR', APP_DIR . '..' . DIRECTORY_SEPARATOR . 'data');
        define('TEST_DATA_DIR', DATA_DIR . DIRECTORY_SEPARATOR . 'tests' . DIRECTORY_SEPARATOR);
        define('TEST_TEMPLATES_DATA_DIR', DATA_DIR. DIRECTORY_SEPARATOR . 'test_templates' . DIRECTORY_SEPARATOR);

        define('WWW_DIR', APP_DIR . '..' . DIRECTORY_SEPARATOR . 'www');
        define('DATA_PUBLIC_DIR', WWW_DIR . DIRECTORY_SEPARATOR . 'data_public');
        define('ASSETS_DIR', WWW_DIR . DIRECTORY_SEPARATOR . 'assets');
        define('LOGOS_DIR', DATA_PUBLIC_DIR . DIRECTORY_SEPARATOR . 'logos');
        define('TMP_DIR', APP_DIR . '..' . DIRECTORY_SEPARATOR . 'temp');
        define('LOGOS_TMP_DIR', TMP_DIR . DIRECTORY_SEPARATOR . 'logos');
        define('NPM_DIR', APP_DIR . '..' . DIRECTORY_SEPARATOR . 'node_modules');
        define('VENDOR_DIR', APP_DIR . '..' . DIRECTORY_SEPARATOR . 'vendor');

        return $configurator->addParameters([
            'appDir' => APP_DIR,
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
    }

    /**
     * @return string
     */
    public static function getEnv(): string
    {
        if (isset($_SERVER['SERVER_ADDR']) && in_array($_SERVER['SERVER_ADDR'], ['127.0.0.1', '::1'], true)) {
            return self::ENV_DEVELOPMENT;
        }
        return self::ENV_PRODUCTION;
    }

    /**
     * @return Configurator
     */
    public static function boot(): Configurator
    {
        $configurator = new Configurator();

        $configurator->setDebugMode(filter_input(INPUT_SERVER, 'HTTP_EDOMPALLOWDEBUG') === 'SJPPFguvBhl9zN84nviZ');
        $configurator->enableTracy(__DIR__ . '/../../log');

        $configurator->setTimeZone('Europe/Prague');
        $configurator->setTempDirectory(__DIR__ . '/../../temp');

        $env = self::getEnv();

        // Load configurations based on environment
        $configurator->addConfig(__DIR__ . '/../Config/config.neon');
        $configurator->addConfig(__DIR__ . '/../Config/config.local.neon');
//        $configurator->addConfig(__DIR__ . '/../Config/config.' . ENVIRONMENT . '.neon');

        if ($env === self::ENV_PRODUCTION && 'https' === getenv('HTTP_X_FORWARDED_PROTO')) {
            \Nette\Http\Url::$defaultPorts['https'] = (int) getenv('SERVER_PORT');
        }

        $configurator = self::setConfiguratorParameters($configurator);

        return $configurator;
    }
}