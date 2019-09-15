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
 * Trait DetectPresenterTrait
 * @package App\Components\Traits
 */
trait DetectPresenterTrait
{
    /**
     * @return bool
     */
    public function isAdminModule(): bool
    {
        return Strings::startsWith($this->presenter->getName(), 'Admin');
    }

    /**
     * @return bool
     */
    public function isProblemTemplatePresenter(): bool
    {
        return (bool) Strings::match($this->presenter->getName(), '~^Admin:.*Template$~');
    }
}