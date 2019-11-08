<?php
/**
 * Created by PhpStorm.
 * User: wiedzmin
 * Date: 17.7.19
 * Time: 21:01
 */

namespace App\Components\LogoDragAndDrop;

use App\CoreModule\Components\EDOMPControl;
use App\Model\Persistent\Repository\LogoRepository;

/**
 * Class LogoDragAndDropControl
 * @package App\Components\LogoDragAndDrop
 */
class LogoDragAndDropControl extends EDOMPControl
{
    /**
     * @var LogoRepository
     */
    protected $logoRepository;

    /**
     * LogoDragAndDropControl constructor.
     * @param LogoRepository $logoRepository
     */
    public function __construct(LogoRepository $logoRepository)
    {
        parent::__construct();
        $this->logoRepository = $logoRepository;
    }

    /**
     * @throws \Exception
     */
    public function render(): void
    {
        $this->template->logos = $this->logoRepository->findAssoc([], 'id');
        parent::render();
    }
}