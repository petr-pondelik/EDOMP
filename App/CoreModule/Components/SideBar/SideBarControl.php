<?php
/**
 * Created by PhpStorm.
 * User: wiedzmin
 * Date: 22.5.19
 * Time: 17:43
 */

namespace App\CoreModule\Components\SideBar;

use App\CoreModule\Components\EDOMPControl;
use App\CoreModule\Model\Persistent\Repository\ThemeRepository;

/**
 * Class SideBarControl
 * @package App\CoreModule\Components\SideBar
 */
class SideBarControl extends EDOMPControl
{
    /**
     * @var ThemeRepository
     */
    protected $themeRepository;

    /**
     * SideBarControl constructor.
     * @param ThemeRepository $themeRepository
     */
    public function __construct(ThemeRepository $themeRepository)
    {
        parent::__construct();
        $this->themeRepository = $themeRepository;
    }

    /**
     * @throws \Doctrine\ORM\Query\QueryException
     * @throws \ReflectionException
     */
    public function render(): void
    {
        if ($this->presenter->user->isInRole('student')) {
            $this->template->themes = $this->presenter->user->identity->themes;
        } else {
            $this->template->themes = $this->themeRepository->findAllowed($this->presenter->user);
        }
        parent::render();
    }
}