<?php
/**
 * Created by PhpStorm.
 * User: wiedzmin
 * Date: 14.2.19
 * Time: 11:05
 */

namespace App\Components\Forms;

use App\Model\Functionality\BaseFunctionality;
use App\Services\ValidationService;
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
     * @var ValidationService
     */
    protected $validationService;

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
     * @param ValidationService $validationService
     */
    public function __construct(ValidationService $validationService)
    {
        parent::__construct();
        $this->validationService = $validationService;
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