<?php
/**
 * Created by PhpStorm.
 * User: wiedzmin
 * Date: 17.7.19
 * Time: 21:01
 */

namespace App\Components\LogoDragAndDrop;

use App\Model\Repository\LogoRepository;
use Nette\Application\UI\Control;

/**
 * Class LogoDragAndDropControl
 * @package App\Components\LogoDragAndDrop
 */
class LogoDragAndDropControl extends Control
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
        $this->logoRepository = $logoRepository;
    }

    /**
     * @throws \Exception
     */
    public function render(): void
    {
        $this->template->logos = $this->logoRepository->findAssoc([], 'id');
        $this->template->render(__DIR__ . '/templates/default.latte');
    }
}