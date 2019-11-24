<?php
/**
 * Created by PhpStorm.
 * User: wiedzmin
 * Date: 9.11.19
 * Time: 11:20
 */

namespace App\CoreModule\DI;


use App\CoreModule\Exceptions\ModuleException;
use App\CoreModule\Interfaces\IModuleExtension;
use Nette\DI\CompilerExtension;
use Nette\DI\ContainerBuilder;

// Documentation:  https://github.com/planette/cookbook-dependency-injection/tree/master/3.0

/**
 * Class ModuleExtension
 * @package App\CoreModule\DI
 */
class ModuleExtension extends CompilerExtension implements IModuleExtension
{
    /**
     * @var array
     */
    protected $defaults = [];

    /**
     * @var array
     */
    protected $configRequiredItems = [];

    /**
     * @throws ModuleException
     */
    public function loadConfiguration(): void
    {
        bdump(static::class . ': loadConfiguration');

        parent::loadConfiguration();

        // Validate and merge config with defaults ($this->config has higher priority, so it overrides $this->default items)
        $this->config = $this->validateEDOMPModuleConfig($this->defaults);
        bdump($this->config);

        // Get DI container builder
        $builder = $this->getContainerBuilder();

        // Add services definitions into DI container
        $this->addDefinitions($builder);
    }

    /**
     * @param array $defaults
     * @return array
     * @throws ModuleException
     */
    public function validateEDOMPModuleConfig(array $defaults): array
    {
        foreach ($this->configRequiredItems as $configRequiredItem) {
            if (!array_key_exists($configRequiredItem, $this->config) || !$this->config[$configRequiredItem]) {
                throw new ModuleException($configRequiredItem . ' required in ' . static::class . ' configuration.');
            }
        }
        return $this->validateConfig($defaults);
    }

    public function addDefinitions(ContainerBuilder $builder): void {}

}