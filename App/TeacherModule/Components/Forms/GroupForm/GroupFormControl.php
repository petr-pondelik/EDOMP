<?php
/**
 * Created by PhpStorm.
 * User: wiedzmin
 * Date: 23.5.19
 * Time: 19:53
 */

namespace App\TeacherModule\Components\Forms\GroupForm;


use App\CoreModule\Arguments\ValidatorArgument;
use App\CoreModule\Components\Forms\EntityFormControl;
use App\CoreModule\Model\Persistent\Functionality\GroupFunctionality;
use App\CoreModule\Model\Persistent\Manager\ConstraintEntityManager;
use App\CoreModule\Model\Persistent\Repository\SuperGroupRepository;
use App\CoreModule\Services\Validator;
use Nette\Application\AbortException;
use Nette\Application\UI\Form;
use Nette\Utils\ArrayHash;

/**
 * Class GroupFormControl
 * @package App\TeacherModule\Components\Forms\GroupForm
 */
final class GroupFormControl extends EntityFormControl
{
    /**
     * @var SuperGroupRepository
     */
    protected $superGroupRepository;

    /**
     * GroupFormControl constructor.
     * @param Validator $validator
     * @param ConstraintEntityManager $entityManager
     * @param GroupFunctionality $groupFunctionality
     * @param SuperGroupRepository $superGroupRepository
     */
    public function __construct
    (
        Validator $validator,
        ConstraintEntityManager $entityManager,
        GroupFunctionality $groupFunctionality,
        SuperGroupRepository $superGroupRepository
    )
    {
        parent::__construct($validator, $entityManager);
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
     * @throws \App\CoreModule\Exceptions\ValidatorException
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
            $values->userId = $this->presenter->user->id;
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
    public function handleUpdateFormSuccess(Form $form, ArrayHash $values): void
    {
        try{
            $this->functionality->update($this->entity->getId(), ArrayHash::from([
                'label' => $values->label,
                'superGroup' => $values->superGroup
            ]));
            $this->onSuccess();
        } catch (\Exception $e){
            bdump($e);
            //The exception that is thrown when user attempts to terminate the current presenter or application. This is special "silent exception" with no error message or code.
            if ($e instanceof AbortException){
                return;
            }
            $this->onError($e);
        }
    }

    public function setDefaults(): void
    {
        $this['form']['id']->setDefaultValue($this->entity->getId());
        $this['form']['label']->setDefaultValue($this->entity->getLabel());
        $this['form']['superGroup']->setDefaultValue($this->entity->getSuperGroup()->getId());
    }
}