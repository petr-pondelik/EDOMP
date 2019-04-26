<?php
/**
 * Created by PhpStorm.
 * User: wiedzmin
 * Date: 13.4.19
 * Time: 14:38
 */

namespace App\Components\Forms;

/**
 * Class LogoFormFactory
 * @package App\Components\Forms
 */
class LogoFormFactory extends BaseForm
{
    /**
     * @return \Nette\Application\UI\Form
     */
    public function create()
    {
        $form = parent::create();

        $form->addText("label", "Název")
            ->setHtmlAttribute("class", "form-control");

        $form->addText("logo_file", "Soubor")
            ->setHtmlAttribute("class", "file-pond-input");

        $form->addSubmit("submit", "Vytvořit")
            ->setHtmlAttribute("class", "btn btn-primary");

        return $form;
    }
}