<?php
/**
 * Created by PhpStorm.
 * User: wiedzmin
 * Date: 5.7.19
 * Time: 21:59
 */

namespace App\Components\Forms\ProblemTemplateForm\LinearEqTemplateForm;


use App\Components\Forms\ProblemTemplateForm\ProblemTemplateFormControl;
use Nette\Application\UI\Form;

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

        $form->addSelect('condition_' . $this->constHelper::RESULT, 'Podmínka výsledku', $resultConditions)
            ->setHtmlAttribute('class', 'form-control condition')
            ->setHtmlId('condition-' . $this->constHelper::RESULT);

        $form['type']->setDefaultValue($this->constHelper::LINEAR_EQ);

        return $form;
    }

    /**
     * @param $body
     * @return mixed|string
     * @throws \App\Exceptions\NewtonApiException
     * @throws \App\Exceptions\NewtonApiRequestException
     * @throws \App\Exceptions\NewtonApiUnreachableException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function standardize($body)
    {
        return $this->mathService->standardizeEquation($body);
    }
}