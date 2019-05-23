<?php
/**
 * Created by PhpStorm.
 * User: wiedzmin
 * Date: 13.4.19
 * Time: 14:38
 */

namespace App\Components\Forms;

use Nette\Application\UI\Form;
use Nette\Utils\ArrayHash;

/**
 * Class LogoFormFactory
 * @package App\Components\Forms
 */
class LogoFormFactory extends BaseFormControl
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

    /**
     * @param Form $form
     */
    public function handleFormValidate(Form $form): void
    {
        // TODO: Implement handleFormValidate() method.
    }

    /**
     * @param Form $form
     * @param ArrayHash $values
     */
    public function handleCreateFormSuccess(Form $form, ArrayHash $values): void
    {
        // TODO: Implement handleCreateFormSuccess() method.
    }

    /**
     * @param Form $form
     * @param ArrayHash $values
     */
    public function handleEditFormSuccess(Form $form, ArrayHash $values): void
    {
        // TODO: Implement handleEditFormSuccess() method.
    }

    public function render(): void
    {
        // TODO: Implement render() method.
    }
}