<?php
/**
 * Created by PhpStorm.
 * User: wiedzmin
 * Date: 26.5.19
 * Time: 9:45
 */

namespace App\Components\Forms\TestStatisticsForm;


use App\Arguments\ValidatorArgument;
use App\Components\Forms\EntityFormControl;
use App\Model\Persistent\Entity\Test;
use App\Model\Persistent\Functionality\ProblemFunctionality;
use App\Model\Persistent\Functionality\ProblemFinalTestVariantAssociationFunctionality;
use App\Services\Validator;
use Nette\Application\UI\Form;
use Nette\Utils\ArrayHash;

/**
 * Class TestStatisticsFormControl
 * @package App\Components\Forms\TestStatisticsForm
 */
class TestStatisticsFormControl extends EntityFormControl
{
    /**
     * @var ProblemFinalTestVariantAssociationFunctionality
     */
    protected $problemFinalTestVariantAssociationFunctionality;

    /**
     * @var Test
     */
    protected $test;

    /**
     * TestStatisticsFormControl constructor.
     * @param Validator $validator
     * @param ProblemFunctionality $problemFunctionality
     * @param ProblemFinalTestVariantAssociationFunctionality $problemFinalTestVariantAssociationFunctionality
     */
    public function __construct
    (
        Validator $validator,
        ProblemFunctionality $problemFunctionality,
        ProblemFinalTestVariantAssociationFunctionality $problemFinalTestVariantAssociationFunctionality
    )
    {
        parent::__construct($validator);
        $this->functionality = $problemFunctionality;
        $this->problemFinalTestVariantAssociationFunctionality = $problemFinalTestVariantAssociationFunctionality;
    }

    /**
     * @return Form
     */
    public function createComponentForm(): Form
    {
        $form = parent::createComponentForm();
        // TODO: Get maximal variants and problems cnt from config
        for ($i = 0; $i < 8; $i++) {
            for($j = 0; $j < 20; $j++){
                $form->addInteger('problem_final_id_disabled_' . $i . '_' . $j, 'ID příkladu')
                    ->setHtmlAttribute('class', 'form-control')
                    ->setDisabled();
                $form->addHidden('problem_final_id_' . $i . '_' . $j);
                $form->addInteger('problem_template_id_disabled_' . $i . '_' . $j, 'ID šablony')
                    ->setHtmlAttribute('class', 'form-control')
                    ->setDisabled();
                $form->addHidden('problem_template_id_' . $i . '_' . $j);
                $form->addText('success_rate_' . $i . '_' . $j, 'Úspěšnost v testu')
                    ->setHtmlAttribute('class', 'form-control')
                    ->setHtmlAttribute('placeholder', 'Zadejte desetinné číslo v intervalu <0; 1>');
            }
        }
        $form->onSuccess[] = [$this, 'handleFormSuccess'];
        return $form;
    }

    /**
     * @param Form $form
     */
    public function handleFormValidate(Form $form): void
    {
        $values = $form->getValues();
        for($i = 0; $i < $this->test->getVariantsCnt(); $i++){
            for($j = 0; $j < $this->test->getProblemsPerVariant(); $j++){
                $validateFields['success_rate'] = new ValidatorArgument($values->{'success_rate_' . $i . '_' . $j}, 'range0to1', 'success_rate_' . $i . '_' . $j);
                $this->validator->validate($form, $validateFields);
            }
        }
        $this->redrawControl('formSnippetArea');
    }

    /**
     * @param Form $form
     * @param ArrayHash $values
     */
    public function handleFormSuccess(Form $form, ArrayHash $values): void
    {
        // Get test variants
        $testVariants = $this->test->getTestVariants()->getValues();

        // Update success rates for in ProblemFinalTestVariantAssociations
        for ($i = 0; $i < $this->test->getVariantsCnt(); $i++) {
            for($j = 0; $j < $this->test->getProblemsPerVariant(); $j++){
                try{
                    $this->problemFinalTestVariantAssociationFunctionality->update(
                        $values->{'problem_final_id_' . $i . '_' . $j},
                        ArrayHash::from([
                            'test_variants_id' => $testVariants[$i]->getId(),
                            'success_rate' => $values->{'success_rate_' . $i . '_' . $j}
                        ])
                    );
                } catch (\Exception $e){
                    bdump($e);
                    $this->onError($e);
                    return;
                }
            }
        }

        // Recalculate success rates for associated ProblemFinals and ProblemTemplates entities
        for ($i = 0; $i < $this->test->getVariantsCnt(); $i++) {
            for($j = 0; $j < $this->test->getProblemsPerVariant(); $j++){
                try{
                    $this->functionality->calculateSuccessRate($values->{'problem_final_id_' . $i . '_' . $j});
                } catch (\Exception $e){
                    bdump($e);
                    $this->onError($e);
                    return;
                }
                if (!empty($values->{'problem_template_id_' . $i . '_' . $j})){
                    try{
                        $this->functionality->calculateSuccessRate($values->{'problem_template_id_' . $i . '_' . $j}, true);
                    } catch (\Exception $e){
                        bdump($e);
                        $this->onError($e);
                        return;
                    }
                }
            }
        }
    }

    /**
     * @param Form $form
     * @param ArrayHash $values
     */
    public function handleEditFormSuccess(Form $form, ArrayHash $values): void
    {
        // TODO: Implement handleEditFormSuccess() method.
    }

    public function setDefaults(): void
    {
        // TODO: Implement setDefaults() method.
    }
}