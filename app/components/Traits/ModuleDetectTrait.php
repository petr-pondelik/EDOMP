<?php
/**
 * Created by PhpStorm.
 * User: wiedzmin
 * Date: 23.5.19
 * Time: 10:16
 */

namespace App\Components\Traits;

use Nette\Utils\Strings;

/**
 * Trait ModuleDetectTrait
 * @package App\Components\Traits
 */
trait ModuleDetectTrait
{
    /**
     * @return bool
     */
    public function isAdminModule(): bool
    {
        return Strings::startsWith($this->presenter->getName(), "Admin");
    }
}