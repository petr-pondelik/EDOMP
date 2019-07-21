<?php
/**
 * Created by PhpStorm.
 * User: wiedzmin
 * Date: 26.5.19
 * Time: 11:54
 */

namespace App\Components\Forms;

use App\Services\Validator;
use Nette\Application\UI\Form;
use Nette\Utils\ArrayHash;

/**
 * Class EntityFormControl
 * @package App\Components\Forms
 */
abstract class EntityFormControl extends FormControl
{
    /**
     * @var bool
     */
    protected $edit;

    /**
     * EntityFormControl constructor.
     * @param Validator $validator
     * @param bool $edit
     */
    public function __construct(Validator $validator, bool $edit = false)
    {
        parent::__construct($validator);
        $this->edit = $edit;
    }

    /**
     * @return Form
     */
    public function createComponentForm(): Form
    {
        $form = parent::createComponentForm();

        if ($this->edit) {
            $form->addInteger('id', 'ID')
                ->setHtmlAttribute('class', 'form-control')
                ->setDisabled();

            $form->addHidden('idHidden');

            $form->onSuccess[] = [$this, 'handleEditFormSuccess'];
        }
        else{
            $form->onSuccess[] = [$this, 'handleFormSuccess'];
        }

        return $form;
    }

    /**
     * @param Form $form
     * @param ArrayHash $values
     */
    abstract public function handleEditFormSuccess(Form $form, ArrayHash $values): void;
}