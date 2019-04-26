<?php
/**
 * Created by PhpStorm.
 * User: wiedzmin
 * Date: 12.4.19
 * Time: 9:48
 */

namespace App\Components\Forms;

/**
 * Class SuperGroupFormFactory
 * @package App\Components\Forms
 */
class SuperGroupFormFactory extends BaseForm
{
    /**
     * @return \Nette\Application\UI\Form
     */
    public function create()
    {
        $form = parent::create();

        $form->addText("label", "Název")
            ->setHtmlAttribute("class", "form-control");

        $form->addSubmit("submit", "Vytvořit")
            ->setHtmlAttribute("class", "btn btn-primary");

        return $form;
    }
}