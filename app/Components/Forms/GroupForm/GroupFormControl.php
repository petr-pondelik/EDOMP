<?php
/**
 * Created by PhpStorm.
 * User: wiedzmin
 * Date: 23.5.19
 * Time: 19:53
 */

namespace App\Components\Forms\GroupForm;


use App\Arguments\ValidatorArgument;
use App\Components\Forms\EntityFormControl;
use App\Model\Persistent\Functionality\GroupFunctionality;
use App\Model\Persistent\Repository\SuperGroupRepository;
use App\Services\Validator;
use Nette\Application\AbortException;
use Nette\Application\UI\Form;
use Nette\Utils\ArrayHash;

/**
 * Class GroupFormControl
 * @package App\Components\Forms\GroupForm
 */
class GroupFormControl extends EntityFormControl
{
    /**
     * @var SuperGroupRepository
     */
    protected $superGroupRepository;

    /**
     * GroupFormControl constructor.
     * @param Validator $validator
     * @param GroupFunctionality $groupFunctionality
     * @param SuperGroupRepository $superGroupRepository
     * @param bool $edit
     */
    public function __construct
    (
        Validator $validator,
        GroupFunctionality $groupFunctionality, SuperGroupRepository $superGroupRepository,
        bool $edit = false
    )
    {
        parent::__construct($validator, $edit);
        $this->functionality = $groupFunctionality;
        $this->superGroupRepository = $superGroupRepository;
    }

    /**
     * @return Form
     * @throws \Doctrine\ORM\Query\QueryException
     */
    public function createComponentForm(): Form
    {
        $form = parent::createComponentForm();

        $superGroupOptions = $this->superGroupRepository->findAllowed($this->presenter->user);

        $form->addText('label', 'Název *')
            ->setHtmlAttribute('class', 'form-control')
            ->setHtmlAttribute('placeholder', 'Zadejte název skupiny.');

        $form->addSelect('superGroup', 'Superskupina *', $superGroupOptions)
            ->setPrompt('Zvolte superskupinu')
            ->setHtmlAttribute('class', 'form-control');

        return $form;
    }

    /**
     * @param Form $form
     */
    public function handleFormValidate(Form $form): void
    {
        $values = $form->values;
        $validateFields['label'] = new ValidatorArgument($values->label, 'stringNotEmpty');
        $validateFields['superGroup'] = new ValidatorArgument($values->superGroup, 'notEmpty');
        $this->validator->validate($form, $validateFields);
        $this->redrawErrors();
    }

    /**
     * @param Form $form
     * @param ArrayHash $values
     */
    public function handleFormSuccess(Form $form, ArrayHash $values): void
    {
        try{
            $values->user_id = $this->presenter->user->id;
            $this->functionality->create($values);
            $this->onSuccess();
        } catch (\Exception $e){
            //The exception that is thrown when user attempts to terminate the current presenter or application. This is special "silent exception" with no error message or code.
            if ($e instanceof AbortException){
                return;
            }
            $this->onError($e);
        }
    }

    /**
     * @param Form $form
     * @param ArrayHash $values
     */
    public function handleEditFormSuccess(Form $form, ArrayHash $values): void
    {
        try{
            $this->functionality->update($values->idHidden, ArrayHash::from([
                'label' => $values->label,
                'superGroup' => $values->superGroup
            ]));
            $this->onSuccess();
        } catch (\Exception $e){
            //The exception that is thrown when user attempts to terminate the current presenter or application. This is special "silent exception" with no error message or code.
            if ($e instanceof AbortException){
                return;
            }
            $this->onError($e);
        }
    }

    public function render(): void
    {
        if ($this->edit){
            $this->template->render(__DIR__ . '/templates/edit.latte');
        }
        else{
            $this->template->render(__DIR__ . '/templates/create.latte');
        }
    }
}