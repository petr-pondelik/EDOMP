<?php
/**
 * Created by PhpStorm.
 * User: wiedzmin
 * Date: 5.7.19
 * Time: 22:46
 */

namespace App\Components\Forms\ProblemTemplateForm\QuadraticEqTemplateForm;

use App\Components\Forms\ProblemTemplateForm\ProblemTemplateFormControl;
use Nette\Application\UI\Form;
use Nette\Utils\ArrayHash;

/**
 * Class QuadraticEqTemplateFormControl
 * @package App\Components\Forms\ProblemTemplateForm\QuadraticEqTemplateForm
 */
class QuadraticEqTemplateFormControl extends ProblemTemplateFormControl
{
    protected $type = 'QuadraticEqTemplateForm';

    /**
     * @return Form
     * @throws \Exception
     */
    public function createComponentForm(): Form
    {
        $form = parent::createComponentForm();

        $discriminantConditions = $this->problemConditionRepository->findAssoc([
            'problemConditionType.id' => $this->constHelper::DISCRIMINANT
        ], 'accessor');

        $form->addText('variable', 'Neznámá *')
            ->setHtmlAttribute('class', 'form-control')
            ->setHtmlAttribute('placeholder', 'Neznámá šablony.')
            ->setHtmlId('variable');

        $form->addSelect('condition_' . $this->constHelper::DISCRIMINANT, 'Podmínka diskriminantu', $discriminantConditions)
            ->setHtmlAttribute('class', 'form-control condition')
            ->setHtmlId('condition-' . $this->constHelper::DISCRIMINANT);

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

        $validateFields = [];

        // Then validate if the entered problem corresponds to the selected type
        $validateFields['type'] = [
            'type_' . $values->type => ArrayHash::from([
                'body' => $values->body,
                'standardized' => $standardized,
                'variable' => $values->variable
            ])
        ];

        try{
            $validationErrors = $this->validationService->validate($validateFields);
        } catch (\Exception $e){
            $form['body']->addError($e->getMessage());
            $this->redrawFormErrors();
            return;
        }

        if($validationErrors){
            foreach($validationErrors as $veKey => $errorGroup){
                foreach($errorGroup as $egKey => $error){
                    $form['body']->addError($error);
                }
            }
            $this->redrawFormErrors();
            return;
        }

        $this->redrawFormErrors();
    }

    public function handleCondValidation(string $body, int $conditionType, int $accessor, int $problemType, string $variable)
    {
        return parent::handleCondValidation($body, $conditionType, $accessor, $problemType, $variable);
    }
}