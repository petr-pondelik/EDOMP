<?php
/**
 * Created by PhpStorm.
 * User: wiedzmin
 * Date: 22.5.19
 * Time: 17:43
 */

namespace App\Components\SideBar;

use App\Components\EDOMPControl;
use App\Components\Traits\DetectPresenterTrait;

/**
 * Class SideBarControl
 * @package App\Components\SideBar
 */
class SideBarControl extends EDOMPControl
{
    use DetectPresenterTrait;

    public function render(): void
    {
        $this->template->adminModule = $this->isAdminModule();
        $this->template->problemTemplatePresenter = $this->isProblemTemplatePresenter();
        $this->template->render(__DIR__ . '/templates/sideBar.latte');
    }
}