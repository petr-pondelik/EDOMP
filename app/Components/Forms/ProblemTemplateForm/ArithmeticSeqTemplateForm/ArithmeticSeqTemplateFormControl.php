<?php
/**
 * Created by PhpStorm.
 * User: wiedzmin
 * Date: 5.7.19
 * Time: 22:58
 */

namespace App\Components\Forms\ProblemTemplateForm\ArithmeticSeqTemplateForm;


use App\Components\Forms\ProblemTemplateForm\ProblemTemplateFormControl;
use Nette\Application\UI\Form;

/**
 * Class ArithmeticSeqTemplateFormControl
 * @package App\Components\Forms\ProblemTemplateForm\ArithmeticSeqTemplateForm
 */
class ArithmeticSeqTemplateFormControl extends ProblemTemplateFormControl
{
    /**
     * @var string
     */
    protected $type = 'ArithmeticSeqTemplateForm';

    public function createComponentForm(): Form
    {
        $form = parent::createComponentForm();

        $form->addInteger('first_n', 'Počet prvních členů *')
            ->setHtmlAttribute('class', 'form-control')
            ->setHtmlAttribute('placeholder', 'Zadejte počet zkoumaných prvních členů.')
            ->setHtmlId('first-n');

        $form['type']->setDefaultValue($this->constHelper::ARITHMETIC_SEQ);

        return $form;
    }

    /**
     * @param $body
     * @return mixed
     */
    public function standardize($body)
    {
        // TODO: Implement standardize() method.
    }
}