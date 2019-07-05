<?php
/**
 * Created by PhpStorm.
 * User: wiedzmin
 * Date: 5.7.19
 * Time: 21:59
 */

namespace App\Components\Forms\ProblemTemplateForm\LinearEqTemplateForm;


use App\Components\Forms\ProblemTemplateForm\ProblemTemplateFormControl;
use App\Exceptions\ProblemTemplateFormatException;
use App\Exceptions\StringFormatException;
use Nette\Application\UI\Form;
use Nette\Utils\ArrayHash;

/**
 * Class LinearEqTemplFormControl
 * @package App\Components\Forms\ProblemTemplateForm\LinearEqTemplateForm
 */
class LinearEqTemplateFormControl extends ProblemTemplateFormControl
{
    protected $type = 'LinearEqTemplateForm';

    /**
     * @return Form
     * @throws \Exception
     */
    public function createComponentForm(): Form
    {
        $form = parent::createComponentForm();

        $resultConditions = $this->problemConditionRepository->findAssoc([
            'problemConditionType.id' => $this->constHelper::RESULT
        ], 'accessor');

        $form->addText('variable', 'Neznámá *')
            ->setHtmlAttribute('class', 'form-control')
            ->setHtmlAttribute('placeholder', 'Neznámá šablony.')
            ->setHtmlId('variable');

        $form->addSelect('condition_' . $this->constHelper::RESULT, 'Podmínka výsledku', $resultConditions)
            ->setHtmlAttribute('class', 'form-control condition')
            ->setHtmlId('condition-' . $this->constHelper::RESULT);

        $form['type']->setDefaultValue($this->constHelper::LINEAR_EQ);

        return $form;
    }

    /**
     * @param Form $form
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function handleFormValidate(Form $form): void
    {
        parent::handleFormValidate($form);

        $values = $form->getValues();

        // If it's the equation template
        try{
            $standardized = $this->mathService->standardizeEquation($values->body);
        } catch (\Exception $e){
            $form['body']->addError($e->getMessage());
            $this->redrawFormErrors();
            return;
        }

        if(!$this->validateType($values, $standardized)){
            return;
        }

        $this->redrawFormErrors();
    }

    /**
     * @param string $body
     * @param int $conditionType
     * @param int $accessor
     * @param int $problemType
     * @param string $variable
     * @param int|null $problemId
     * @return bool|void
     * @throws \App\Exceptions\NewtonApiException
     * @throws \App\Exceptions\NewtonApiRequestException
     * @throws \App\Exceptions\NewtonApiUnreachableException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function handleCondValidation(string $body, int $conditionType, int $accessor, int $problemType, string $variable, int $problemId = null)
    {
        if(!parent::handleCondValidation($body, $conditionType, $accessor, $problemType, $variable)){
            $this->redrawFormErrors();
            return;
        }

        try{
            $standardized = $this->mathService->standardizeEquation($body);
        } catch (StringFormatException $e){
            $this['form']['body']->addError($e->getMessage());
            $this->redrawFormErrors();
            return;
        }

        if(!$this->validateType(ArrayHash::from([ 'type' => $problemType, 'variable' => $variable ]), $standardized)){
            return;
        }

        $validationFields = [];

        // Then validate specified condition
        $validationFields['condition'] = [
            'condition_' . $conditionType => ArrayHash::from([
                'body' => $body,
                'standardized' => $standardized,
                'accessor' => $accessor,
                'variable' => $variable
            ])
        ];

        // Validate template condition
        try{
            $validationErrors = $this->validationService->conditionValidate($validationFields, $problemId ?? null);
        } catch (ProblemTemplateFormatException $e){
            $this['form']['body']->addError($e->getMessage());
            $this->redrawFormErrors();
            return;
        }

        if($validationErrors){
            foreach($validationErrors as $veKey => $errorGroup){
                foreach($errorGroup as $egKey => $error){
                    $this['form']['condition_' . $conditionType]->addError($error);
                }
            }
        }

        $this->redrawFormErrors();

        // If validation succeeded, return true in payload
        if(!$validationErrors){
            $this->flashMessage('Podmínka je splnitelná.', 'success');
            $this->presenter->payload->result = true;
        }
    }
}