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
        $appDir = __DIR__ . DIRECTORY_SEPARATOR . '..';
        $wwwDir = $appDir . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'www';

        $dataDir = $appDir . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'data';
        $tmpDir = $appDir . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'temp';
        $dataPublicDir = $wwwDir . DIRECTORY_SEPARATOR . 'data_public';

        $logosDir = $dataPublicDir . DIRECTORY_SEPARATOR . 'logos';
        $logosTmpDir = $tmpDir . DIRECTORY_SEPARATOR . 'logos';

        $coreModuleDir = $appDir . DIRECTORY_SEPARATOR . 'CoreModule';
        $coreModuleTemplatesDir = $coreModuleDir . DIRECTORY_SEPARATOR . 'templates';

        $studentModuleDir = $appDir . DIRECTORY_SEPARATOR . 'StudentModule';
        $studentModuleTemplatesDir = $studentModuleDir . DIRECTORY_SEPARATOR . 'templates';

        $teacherModuleDir = $appDir . DIRECTORY_SEPARATOR . 'TeacherModule';
        $teacherModuleTemplatesDir = $teacherModuleDir . DIRECTORY_SEPARATOR . 'templates';

        $testDataDir = $dataDir . DIRECTORY_SEPARATOR . 'tests' . DIRECTORY_SEPARATOR;
        $testTemplatesDataDir = $dataDir . DIRECTORY_SEPARATOR . 'test_templates' . DIRECTORY_SEPARATOR;

        return $configurator->addParameters([
            'appDir' => $appDir,
            'wwwDir' => $wwwDir,
            'coreTemplatesDir' => $coreModuleTemplatesDir,
            'studentTemplatesDir' => $studentModuleTemplatesDir,
            'teacherTemplatesDir' => $teacherModuleTemplatesDir,
            'dataPublicDir' => $dataPublicDir,
            'logosDir' => $logosDir,
            'logosTmpDir' => $logosTmpDir,
            'testDataDir' => $testDataDir,
            'testTemplatesDataDir' => $testTemplatesDataDir
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
        $configurator->addConfig(__DIR__ . '/../Config/config.' . $env . '.neon');

        if ($env === self::ENV_PRODUCTION && 'https' === getenv('HTTP_X_FORWARDED_PROTO')) {
            \Nette\Http\Url::$defaultPorts['https'] = (int) getenv('SERVER_PORT');
        }

        $configurator = self::setConfiguratorParameters($configurator);

        return $configurator;
    }
}