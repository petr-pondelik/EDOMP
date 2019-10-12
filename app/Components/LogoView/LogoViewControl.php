<?php
/**
 * Created by PhpStorm.
 * User: wiedzmin
 * Date: 21.9.19
 * Time: 17:02
 */

namespace App\Components\LogoView;

use App\Components\EDOMPControl;
use App\Model\Persistent\Entity\Logo;

/**
 * Class LogoViewControl
 * @package App\Components\LogoView
 */
class LogoViewControl extends EDOMPControl
{
    /**
     * @var Logo
     */
    protected $logo;

    /**
     * @param Logo $logo
     */
    public function setLogo(Logo $logo): void
    {
        $this->logo = $logo;
    }

    public function render(): void
    {
        bdump('RENDER LOGO VIEW');
        $this->template->logo = $this->logo;
        parent::render();
    }
}