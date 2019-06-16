<?php
/**
 * Created by PhpStorm.
 * User: wiedzmin
 * Date: 22.5.19
 * Time: 16:53
 */

namespace App\Components\HeaderBar;

use App\Components\Traits\ModuleDetectTrait;
use Nette\Application\UI\Control;

/**
 * Class HeaderBarControl
 * @package App\Components\HeaderBar
 */
class HeaderBarControl extends Control
{
    use ModuleDetectTrait;

    public function render(): void
    {
        $this->template->adminModule = $this->isAdminModule();
        $this->template->render(__DIR__ . '/templates/headerBar.latte');
    }
}