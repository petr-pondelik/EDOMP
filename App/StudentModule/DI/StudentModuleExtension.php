<?php
/**
 * Created by PhpStorm.
 * User: wiedzmin
 * Date: 9.11.19
 * Time: 17:13
 */

namespace App\StudentModule\DI;

use App\CoreModule\DI\ModuleExtension;
use App\StudentModule\Components\Forms\ProblemFilterForm\IProblemFilterFormFactory;
use Nette\DI\ContainerBuilder;

/**
 * Class StudentModuleExtension
 * @package App\StudentModule\DI
 */
class StudentModuleExtension extends ModuleExtension
{
    /**
     * @param ContainerBuilder $builder
     */
    public function addDefinitions(ContainerBuilder $builder): void
    {
        // Factories definitions

        $builder->addDefinition($this->prefix('problemFilterFormFactory'))
            ->setImplement(IProblemFilterFormFactory::class);
    }
}