<?php
/**
 * Created by PhpStorm.
 * User: wiedzmin
 * Date: 14.2.19
 * Time: 11:05
 */

namespace App\Components\Forms;

use App\Model\Persistent\Functionality\BaseFunctionality;
use App\Services\Validator;
use Nette\Application\UI\Control;
use Nette\Application\UI\Form;
use Nette\Utils\ArrayHash;

/**
 * Class BaseFormControl
 * @package App\Components\Forms
 */
abstract class FormControl extends Control
{
    /**
     * @var BaseFunctionality
     */
    protected $functionality;

    /**
     * @var Validator
     */
    protected $validator;

    /**
     * @var array
     */
    public $onSuccess = [];

    /**
     * @var array
     */
    public $onError = [];

    /**
     * BaseFormControl constructor.
     * @param Validator $validator
     */
    public function __construct(Validator $validator)
    {
        parent::__construct();
        $this->validator = $validator;
    }

    /**
     * @return Form
     */
    public function createComponentForm(): Form
    {
        $form = new Form();

        $renderer = $form->getRenderer();
        $renderer->wrappers['controls']['container'] = NULL;
        $renderer->wrappers['pair']['container'] = 'div class=form-group';
        $renderer->wrappers['pair']['.error'] = 'has-error';
        $renderer->wrappers['control']['container'] = 'div class=col-md-12';
        $renderer->wrappers['label']['container'] = 'div class="control-label col-md-12"';
        $renderer->wrappers['control']['description'] = 'span class=help-block';
        $renderer->wrappers['control']['errorcontainer'] = 'span class=help-block';

        $form->getElementPrototype()->class('form-horizontal ajax');

        $form->addSubmit("submit", "Vytvořit")
            ->setHtmlAttribute("class", "btn btn-primary btn-sm");

        $form->onValidate[] = [$this, 'handleFormValidate'];

        return $form;
    }

    /**
     * @param bool $submitted
     */
    public function redrawErrors(bool $submitted = true): void
    {
        $form = $this['form'];
        $values = $form->getValues();
        //bdump($form->getErrors());
        //bdump($values);
        if($submitted){
            if($form->isSubmitted()){
                foreach ($values as $key => $value){
                    $this->redrawControl($key . 'ErrorSnippet');
                }
            }
        }
        else{
            foreach ($values as $key => $value){
                $this->redrawControl($key . 'ErrorSnippet');
            }
        }
    }

    /**
     * @param Form $form
     */
    abstract public function handleFormValidate(Form $form): void;

    /**
     * @param Form $form
     * @param ArrayHash $values
     */
    abstract public function handleFormSuccess(Form $form, ArrayHash $values): void;

    abstract public function render(): void;
}