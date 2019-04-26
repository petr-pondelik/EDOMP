<?php
/**
 * Created by PhpStorm.
 * User: wiedzmin
 * Date: 12.4.19
 * Time: 11:40
 */

namespace App\Components\Forms;

use App\Model\Managers\SuperGroupManager;

/**
 * Class GroupFormFactory
 * @package App\Components\Forms
 */
class GroupFormFactory extends BaseForm
{
    /**
     * @var SuperGroupManager
     */
    protected $superGroupManager;

    /**
     * GroupFormFactory constructor.
     * @param SuperGroupManager $superGroupManager
     */
    public function __construct
    (
        SuperGroupManager $superGroupManager
    )
    {
        $this->superGroupManager = $superGroupManager;
    }

    /**
     * @return \Nette\Application\UI\Form
     */
    public function create()
    {
        $form = parent::create();

        $superGroupOptions = $this->superGroupManager->getAllPairs("ASC");

        $form->addText("label", "Název")
            ->setHtmlAttribute("class", "form-control");

        $form->addSelect("super_group_id", "Super-Skupina", $superGroupOptions)
            ->setHtmlAttribute("class", "form-control");

        $form->addSubmit("submit", "Vytvořit")
            ->setHtmlAttribute("class", "btn btn-primary");

        return $form;
    }
}