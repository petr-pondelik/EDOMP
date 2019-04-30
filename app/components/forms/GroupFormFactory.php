<?php
/**
 * Created by PhpStorm.
 * User: wiedzmin
 * Date: 12.4.19
 * Time: 11:40
 */

namespace App\Components\Forms;

use App\Model\Managers\SuperGroupManager;
use App\Model\Repository\SuperGroupRepository;

/**
 * Class GroupFormFactory
 * @package App\Components\Forms
 */
class GroupFormFactory extends BaseForm
{
    /**
     * @var SuperGroupRepository
     */
    protected $superGroupRepository;

    /**
     * GroupFormFactory constructor.
     * @param SuperGroupRepository $superGroupRepository
     */
    public function __construct
    (
        SuperGroupRepository $superGroupRepository
    )
    {
        $this->superGroupRepository = $superGroupRepository;
    }

    /**
     * @return \Nette\Application\UI\Form
     * @throws \Exception
     */
    public function create()
    {
        $form = parent::create();

        $superGroupOptions = $this->superGroupRepository->findAssoc([], "id");

        $form->addText("label", "Název")
            ->setHtmlAttribute("class", "form-control");

        $form->addSelect("super_group_id", "Super-Skupina", $superGroupOptions)
            ->setHtmlAttribute("class", "form-control");

        $form->addSubmit("submit", "Vytvořit")
            ->setHtmlAttribute("class", "btn btn-primary");

        return $form;
    }
}