<?php
/**
 * Created by PhpStorm.
 * User: wiedzmin
 * Date: 24.5.19
 * Time: 12:40
 */

namespace App\TeacherModule\Components\Forms\PermissionForm;


use App\CoreModule\Components\Forms\FormControl;
use App\CoreModule\Model\Persistent\Entity\BaseEntity;
use App\CoreModule\Model\Persistent\Functionality\GroupFunctionality;
use App\CoreModule\Model\Persistent\Functionality\SuperGroupFunctionality;
use App\CoreModule\Model\Persistent\Repository\ThemeRepository;
use App\CoreModule\Services\Validator;
use Nette\Application\AbortException;
use Nette\Application\UI\Form;
use Nette\Utils\ArrayHash;

/**
 * Class PermissionFormControl
 * @package App\TeacherModule\Components\Forms\PermissionForm
 */
class PermissionFormControl extends FormControl
{
    /**
     * @var SuperGroupFunctionality
     */
    protected $superGroupFunctionality;

    /**
     * @var ThemeRepository
     */
    protected $themeRepository;

    /**
     * @var BaseEntity
     */
    protected $entity;

    /**
     * @var bool
     */
    protected $super;

    /**
     * PermissionFormControl constructor.
     * @param Validator $validator
     * @param GroupFunctionality $groupFunctionality
     * @param SuperGroupFunctionality $superGroupFunctionality
     * @param ThemeRepository $themeRepository
     * @param bool $super
     */
    public function __construct
    (
        Validator $validator,
        GroupFunctionality $groupFunctionality, SuperGroupFunctionality $superGroupFunctionality,
        ThemeRepository $themeRepository,
        bool $super = false
    )
    {
        parent::__construct($validator);
        $this->functionality = $groupFunctionality;
        $this->superGroupFunctionality = $superGroupFunctionality;
        $this->themeRepository = $themeRepository;
        $this->super = $super;
    }

    /**
     * @return Form
     * @throws \Exception
     */
    public function createComponentForm(): Form
    {
        $form = parent::createComponentForm();
        $themeOptions = $this->themeRepository->findAssoc([], 'id');
        $form->addMultiSelect('themes', 'Téma', $themeOptions)
            ->setHtmlAttribute('class', 'form-control selectpicker')
            ->setHtmlAttribute('title', 'Zvolte témata');
        $form['submit']->caption = 'Uložit';
        if ($this->super){
            $form->onSuccess[] = [$this, 'handleSuperFormSuccess'];
        }
        else{
            $form->onSuccess[] = [$this, 'handleFormSuccess'];
        }
        return $form;
    }

    /**
     * @param Form $form
     */
    public function handleFormValidate(Form $form): void {}

    /**
     * @param Form $form
     * @param ArrayHash $values
     */
    public function handleFormSuccess(Form $form, ArrayHash $values): void
    {
        bdump('HANDLE FORM SUCCESS');
        try{
            $this->functionality->updatePermissions($this->entity->getId(), $values->themes);
            $this->onSuccess();
        } catch (\Exception $e){
            // The exception that is thrown when user attempts to terminate the current presenter or application. This is special "silent exception" with no error message or code.
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
    public function handleSuperFormSuccess(Form $form, ArrayHash $values): void
    {
        bdump('HANDLE SUPER FORM SUCCESS');
        try{
            $this->superGroupFunctionality->updatePermissions($this->entity->getId(), $values->themes);
            $this->onSuccess();
        } catch (\Exception $e){
            // The exception that is thrown when user attempts to terminate the current presenter or application. This is special "silent exception" with no error message or code.
            if ($e instanceof AbortException){
                return;
            }
            $this->onError($e);
        }
    }

    /**
     * @param BaseEntity $entity
     */
    public function setEntity(BaseEntity $entity): void
    {
        $this->entity = $entity;
        $this->template->entity = $entity;
        $this->setDefaults($entity);
    }

    /**
     * @param BaseEntity $entity
     */
    public function setDefaults(BaseEntity $entity): void
    {
        $this['form']['themes']->setDefaultValue($entity->getThemesId());
    }
}