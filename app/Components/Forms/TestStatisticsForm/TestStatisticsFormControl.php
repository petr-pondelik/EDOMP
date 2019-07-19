<?php
/**
 * Created by PhpStorm.
 * User: wiedzmin
 * Date: 26.5.19
 * Time: 9:45
 */

namespace App\Components\Forms\TestStatisticsForm;


use App\Components\Forms\FormControl;
use App\Model\Entity\Test;
use App\Model\Functionality\ProblemFunctionality;
use App\Model\Functionality\ProblemFinalTestVariantAssociationFunctionality;
use App\Model\Repository\TestRepository;
use App\Services\ValidationService;
use Nette\Application\UI\Form;
use Nette\Utils\ArrayHash;

/**
 * Class TestStatisticsFormControl
 * @package App\Components\Forms\TestStatisticsForm
 */
class TestStatisticsFormControl extends FormControl
{
    /**
     * @var TestRepository
     */
    protected $testRepository;

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
     * @param ValidationService $validationService
     * @param ProblemFunctionality $problemFunctionality
     * @param ProblemFinalTestVariantAssociationFunctionality $problemFinalTestVariantAssociationFunctionality
     * @param TestRepository $testRepository
     * @param int $testId
     */
    public function __construct
    (
        ValidationService $validationService,
        ProblemFunctionality $problemFunctionality,
        ProblemFinalTestVariantAssociationFunctionality $problemFinalTestVariantAssociationFunctionality,
        TestRepository $testRepository,
        int $testId
    )
    {
        parent::__construct($validationService);
        $this->functionality = $problemFunctionality;
        $this->problemFinalTestVariantAssociationFunctionality = $problemFinalTestVariantAssociationFunctionality;
        $this->testRepository = $testRepository;
        $this->test = $this->testRepository->find($testId);
    }

    /**
     * @return Form
     */
    public function createComponentForm(): Form
    {
        $form = parent::createComponentForm();
        $form->addHidden('test_id');
        $form->addHidden('variants_cnt');
        $form->addHidden('problems_per_variant');
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
        for($i = 0; $i < $values->variants_cnt; $i++){
            for($j = 0; $j < $values->problems_per_variant; $j++){
                $validateFields['success_rate'] = $values->{'success_rate_' . $i . '_' . $j};
                $validationErrors = $this->validationService->validate($validateFields);
                if($validationErrors){
                    foreach($validationErrors as $veKey => $errorGroup){
                        foreach($errorGroup as $egKey => $error){
                            $form['success_rate_' . $i . '_' . $j]->addError($error);
                        }
                    }
                }
            }
        }
        $this->redrawControl('successRateSnippetArea');
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
        for ($i = 0; $i < $values->variants_cnt; $i++) {
            for($j = 0; $j < $values->problems_per_variant; $j++){
                try{
                    $this->problemFinalTestVariantAssociationFunctionality->update(
                        $values->{'problem_final_id_' . $i . '_' . $j},
                        ArrayHash::from([
                            'test_variants_id' => $testVariants[$i]->getId(),
                            'success_rate' => $values->{'success_rate_' . $i . '_' . $j}
                        ])
                    );
                } catch (\Exception $e){
                    bdump($e->getMessage());
                    $this->onError($e);
                    return;
                }
            }
        }

        // Recalculate success rates for associated ProblemFinals and ProblemTemplates entities
        for ($i = 0; $i < $values->variants_cnt; $i++) {
            for($j = 0; $j < $values->problems_per_variant; $j++){
                try{
                    $this->functionality->calculateSuccessRate($values->{'problem_final_id_' . $i . '_' . $j});
                } catch (\Exception $e){
                    bdump($e->getMessage());
                    $this->onError($e);
                    return;
                }
                if (!empty($values->{'problem_template_id_' . $i . '_' . $j})){
                    try{
                        $this->functionality->calculateSuccessRate($values->{'problem_template_id_' . $i . '_' . $j}, true);
                    } catch (\Exception $e){
                        $this->onError($e);
                        return;
                    }
                }
            }
        }

        $this->onSuccess();
    }

    public function render(): void
    {
        $this->template->test = $this->test;
        $this->template->render(__DIR__ . '/templates/default.latte');
    }
}