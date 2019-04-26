<?php
/**
 * Created by PhpStorm.
 * User: wiedzmin
 * Date: 23.4.19
 * Time: 8:23
 */

namespace App\Components\Forms;

/**
 * Class TestStatisticsFormFactory
 * @package App\Components\Forms
 */
class TestStatisticsFormFactory extends BaseForm
{
    public function create()
    {
        $form = parent::create();

        $form->addHidden("problems_cnt");

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
}