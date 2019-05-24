<?php
/**
 * Created by PhpStorm.
 * User: wiedzmin
 * Date: 23.4.19
 * Time: 8:23
 */

namespace App\Components\Forms;

use Nette\Application\UI\Form;
use Nette\Utils\ArrayHash;

/**
 * Class TestStatisticsFormFactory
 * @package App\Components\Forms
 */
class TestStatisticsFormFactory extends BaseFormControl
{
    public function create()
    {
        $form = parent::create();

        $form->addHidden("problems_cnt");
        $form->addHidden("test_id");

        for($i = 0; $i < 160; $i++){
            $form->addInteger("problem_final_id_disabled_" . $i, "ID příkladu")
                ->setHtmlAttribute("class", "form-control")
                ->setDisabled();
            $form->addHidden("problem_final_id_" . $i);
            $form->addInteger("problem_prototype_id_disabled_" . $i, "ID šablony")
                ->setHtmlAttribute("class", "form-control")
                ->setDisabled();
            $form->addHidden("problem_prototype_id_" . $i);
            $form->addText("success_rate_" . $i, "Úspěšnost v testu")
                ->setHtmlAttribute("class", "form-control");
        }

        $form->addSubmit("submit", "Uložit");

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