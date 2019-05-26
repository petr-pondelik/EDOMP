<?php
/**
 * Created by PhpStorm.
 * User: wiedzmin
 * Date: 22.5.19
 * Time: 17:43
 */

namespace App\Components\SideBar;

use App\Components\Traits\ModuleDetectTrait;
use Nette\Application\UI\Control;
use Nette\Utils\Strings;

/**
 * Class SideBarControl
 * @package App\Components\SideBar
 */
class SideBarControl extends Control
{
    use ModuleDetectTrait;

    public function render(): void
    {
        $this->template->adminModule = $this->isAdminModule();
        $this->template->render(__DIR__ . '/templates/sideBar.latte');
    }
}