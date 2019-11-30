<?php
/**
 * Created by PhpStorm.
 * User: wiedzmin
 * Date: 17.7.19
 * Time: 21:01
 */

namespace App\TeacherModule\Components\LogoDragAndDrop;

use App\CoreModule\Components\EDOMPControl;
use App\CoreModule\Model\Persistent\Entity\Logo;

/**
 * Class LogoDragAndDropControl
 * @package App\TeacherModule\Components\LogoDragAndDrop
 */
class LogoDragAndDropControl extends EDOMPControl
{
    /**
     * @var Logo[]
     */
    protected $logos;

    /**
     * @param array $logos
     * @throws \ReflectionException
     */
    public function render(array $logos = null): void
    {
        $this->template->logos = $this->logos;
        parent::render();
    }

    /**
     * @return Logo[]
     */
    public function getLogos(): array
    {
        return $this->logos;
    }

    /**
     * @param Logo[] $logos
     */
    public function setLogos(array $logos): void
    {
        $this->logos = $logos;
    }
}