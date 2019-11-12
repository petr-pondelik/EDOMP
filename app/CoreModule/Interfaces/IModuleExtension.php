<?php
/**
 * Created by PhpStorm.
 * User: wiedzmin
 * Date: 9.11.19
 * Time: 11:18
 */

namespace App\CoreModule\Interfaces;

use Nette\DI\ContainerBuilder;

/**
 * Interface IModuleExtension
 * @package App\CoreModule\Interfaces
 */
interface IModuleExtension
{
    /**
     * @param ContainerBuilder $builder
     */
    public function addDefinitions(ContainerBuilder $builder): void;
}