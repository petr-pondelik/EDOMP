<?php
/**
 * Created by PhpStorm.
 * User: wiedzmin
 * Date: 26.5.19
 * Time: 9:45
 */

namespace App\Components\Forms\TestStatisticsForm;


use App\Components\Forms\FormControl;
use App\Model\Functionality\ProblemFunctionality;
use App\Model\Functionality\ProblemTestAssociationFunctionality;
use App\Model\Repository\ProblemTestAssociationRepository;
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
     * @var ProblemTestAssociationRepository
     */
    protected $problemTestAssociationRepository;

    /**
     * @var ProblemTestAssociationFunctionality
     */
    protected $problemTestAssociationFunctionality;

    /**
     * @var int
     */
    protected $testId;

    /**
     * TestStatisticsFormControl constructor.
     * @param ValidationService $validationService
     * @param ProblemFunctionality $problemFunctionality
     * @param ProblemTestAssociationRepository $problemTestAssociationRepository
     * @param ProblemTestAssociationFunctionality $problemTestAssociationFunctionality
     * @param int $testId
     */
    public function __construct
    (
        ValidationService $validationService,
        ProblemFunctionality $problemFunctionality,
        ProblemTestAssociationRepository $problemTestAssociationRepository, ProblemTestAssociationFunctionality $problemTestAssociationFunctionality,
        int $testId
    )
    {
        parent::__construct($validationService);
        $this->functionality = $problemFunctionality;
        $this->problemTestAssociationRepository = $problemTestAssociationRepository;
        $this->problemTestAssociationFunctionality = $problemTestAssociationFunctionality;
        $this->testId = $testId;
    }

    /**
     * @return Form
     */
    public function createComponentForm(): Form
    {
        $form = parent::createComponentForm();
        $form->addHidden('problems_cnt');
        $form->addHidden('test_id');
        for ($i = 0; $i < 160; $i++) {
            $form->addInteger('problem_final_id_disabled_' . $i, 'ID příkladu')
                ->setHtmlAttribute('class', 'form-control')
                ->setDisabled();
            $form->addHidden('problem_final_id_' . $i);
            $form->addInteger('problem_prototype_id_disabled_' . $i, 'ID šablony')
                ->setHtmlAttribute('class', 'form-control')
                ->setDisabled();
            $form->addHidden('problem_prototype_id_' . $i);
            $form->addText('success_rate_' . $i, 'Úspěšnost v testu')
                ->setHtmlAttribute('class', 'form-control');
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
        for($i = 0; $i < $values->problems_cnt; $i++){
            $validateFields['success_rate'] = $values->{'success_rate_' . $i};
            $validationErrors = $this->validationService->validate($validateFields);
            if($validationErrors){
                foreach($validationErrors as $veKey => $errorGroup){
                    foreach($errorGroup as $egKey => $error){
                        $form['success_rate_' . $i]->addError($error);
                    }
                }
            }
            $this->redrawControl('successRateSnippetArea');
        }
    }

    /**
     * @param Form $form
     * @param ArrayHash $values
     */
    public function handleFormSuccess(Form $form, ArrayHash $values): void
    {
        for ($i = 0; $i < $values->problems_cnt; $i++) {
            try{
                $this->problemTestAssociationFunctionality->update(
                    $values->{'problem_final_id_' . $i},
                    ArrayHash::from([
                        'test_id' => $values->test_id,
                        'success_rate' => $values->{'success_rate_' . $i}
                    ])
                );
            } catch (\Exception $e){
                $this->onError($e);
            }
        }

        for ($i = 0; $i < $values->problems_cnt; $i++) {
            try{
                $this->functionality->calculateSuccessRate($values->{'problem_final_id_' . $i});
            } catch (\Exception $e){
                $this->onError();
            }
            if (!empty($values->{'problem_prototype_id_' . $i})){
                try{
                    $this->functionality->calculateSuccessRate($values->{'problem_prototype_id_' . $i}, true);
                } catch (\Exception $e){
                    $this->onError($e);
                }
            }
        }

        $this->onSuccess();
    }

    public function render(): void
    {
        $problemAssociations = $this->problemTestAssociationRepository->findBy(['test' => $this->testId]);
        $this->template->id = $this->testId;
        $this->template->problemsCnt = count($problemAssociations);
        $this->template->problemAssociations = $problemAssociations;
        $this->template->render(__DIR__ . '/templates/default.latte');
    }
}