<?php
/**
 * Created by PhpStorm.
 * User: wiedzmin
 * Date: 19.12.19
 * Time: 12:39
 */

namespace App\CoreModule\Components\FlashesModal;

use App\CoreModule\Components\EDOMPControl;

/**
 * Class FlashesModalControl
 * @package App\CoreModule\Components\FlashesModal
 */
class FlashesModalControl extends EDOMPControl
{
    public function render(): void
    {
        bdump($this->presenter->hasFlashSession());
        $this->template->id = 'flashes-modal';
        $this->template->size = 'lg';
        $this->template->labelItem = 'flashes-modal-label';
        $this->template->bodyStyle = '';
        $this->presenter->hasFlashSession() ? $this->template->show = 'true' : $this->template->show = 'false';
        parent::render();
    }
}