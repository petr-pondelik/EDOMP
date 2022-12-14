<?php
/**
 * Created by PhpStorm.
 * User: wiedzmin
 * Date: 14.2.19
 * Time: 11:05
 */

namespace App\CoreModule\Components\Forms;

use App\CoreModule\Components\EDOMPControl;
use App\CoreModule\Model\Persistent\Functionality\BaseFunctionality;
use App\CoreModule\Services\Validator;
use Nette\Application\UI\Form;
use Nette\Forms\Controls\BaseControl;
use Nette\Forms\IControl;
use Nette\Utils\ArrayHash;

/**
 * Class FormControl
 * @package App\CoreModule\Components\Forms
 */
abstract class FormControl extends EDOMPControl
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
        $renderer->wrappers['controls']['container'] = null;
        $renderer->wrappers['pair']['container'] = 'div class=form-group';
        $renderer->wrappers['pair']['.error'] = 'has-error';
        $renderer->wrappers['control']['container'] = 'div class=col-md-12';
        $renderer->wrappers['label']['container'] = 'div class="control-label col-md-12"';
        $renderer->wrappers['control']['description'] = 'span class=help-block';
        $renderer->wrappers['control']['errorcontainer'] = 'span class=help-block';

        $form->getElementPrototype()->class('form-horizontal ajax');

        $form->addSubmit('submit', 'Vytvořit')
            ->setHtmlAttribute('class', 'btn btn-primary btn-sm');

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
        if ($submitted) {
            if ($form->isSubmitted()) {
                foreach ($values as $key => $value) {
                    $this->redrawControl($key . 'ErrorSnippet');
                }
            }
        } else {
            foreach ($values as $key => $value) {
                $this->redrawControl($key . 'ErrorSnippet');
            }
        }
    }

    public function redrawFlashes(): void
    {
        $this->redrawControl('flashesSnippet');
        $this->presenter->redrawControl('flashModal');
    }

    /**
     * @return bool
     */
    public function isSubmitted(): bool
    {
        return (bool)$this['form']->isSubmitted();
    }

    /**
     * @return string
     */
    public function getAction(): string
    {
        return $this->presenter->getAction();
    }

    /**
     * @return bool
     */
    public function isUpdate(): bool
    {
        return $this->getAction() === 'update';
    }

    /**
     * @return bool
     */
    public function isDefault(): bool
    {
        return $this->getAction() === 'default';
    }

    /**
     * @return bool
     */
    public function isCreate(): bool
    {
        return $this->getAction() === 'create';
    }

    /**
     * @param Form $form
     * @return BaseControl|null
     */
    public function getFirstErrorComponent(Form $form): ?BaseControl
    {
        $components = $form->getComponents();
        foreach ($components as $component) {
            if ($component instanceof BaseControl) {
                /** @var BaseControl $component */
                if ($component->getErrors()) {
                    return $component;
                }
            }
        }
        return null;
    }

    /**
     * @param string $errorName
     */
    public function setFormErrorPayload(string $errorName): void
    {
        $this->presenter->setPayload('formError', true, [
            'name' => $errorName
        ]);
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
}