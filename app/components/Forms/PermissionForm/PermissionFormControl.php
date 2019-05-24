<?php
/**
 * Created by PhpStorm.
 * User: wiedzmin
 * Date: 24.5.19
 * Time: 12:40
 */

namespace App\Components\Forms\PermissionForm;


use App\Components\Forms\BaseFormControl;
use App\Model\Functionality\GroupFunctionality;
use App\Model\Functionality\SuperGroupFunctionality;
use App\Model\Repository\CategoryRepository;
use App\Service\ValidationService;
use Nette\Application\AbortException;
use Nette\Application\UI\Form;
use Nette\Utils\ArrayHash;

/**
 * Class PermissionFormControl
 * @package App\Components\Forms\PermissionForm
 */
class PermissionFormControl extends BaseFormControl
{
    /**
     * @var SuperGroupFunctionality
     */
    protected $superGroupFunctionality;

    /**
     * @var CategoryRepository
     */
    protected $categoryRepository;

    /**
     * PermissionFormControl constructor.
     * @param ValidationService $validationService
     * @param GroupFunctionality $groupFunctionality
     * @param SuperGroupFunctionality $superGroupFunctionality
     * @param CategoryRepository $categoryRepository
     * @param bool $edit
     * @param bool $super
     */
    public function __construct
    (
        ValidationService $validationService,
        GroupFunctionality $groupFunctionality, SuperGroupFunctionality $superGroupFunctionality,
        CategoryRepository $categoryRepository,
        bool $edit = false, bool $super = false
    )
    {
        parent::__construct($validationService, $edit, $super);
        $this->functionality = $groupFunctionality;
        $this->superGroupFunctionality = $superGroupFunctionality;
        $this->categoryRepository = $categoryRepository;
    }

    /**
     * @return Form
     * @throws \Exception
     */
    public function createComponentForm(): Form
    {
        $form = parent::createComponentForm();

        $categoryOptions = $this->categoryRepository->findAssoc([], "id");

        $form->addHidden("id");

        $form->addMultiSelect("categories", "Kategorie", $categoryOptions)
            ->setHtmlAttribute("class", "form-control selectpicker");

        $form->addSubmit("submit", "Uložit")
            ->setHtmlAttribute("class", "btn btn-primary btn-sm");

        if ($this->super)
            $form->onSuccess[] = [$this, 'handleSuperGroupFormSuccess'];
        else
            $form->onSuccess[] = [$this, 'handleGroupFormSuccess'];

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
    public function handleGroupFormSuccess(Form $form, ArrayHash $values): void
    {
        try{
            $this->functionality->updatePermissions($values->id, $values->categories);
            $this->onSuccess();
        } catch (\Exception $e){
            //The exception that is thrown when user attempts to terminate the current presenter or application. This is special "silent exception" with no error message or code.
            if ($e instanceof AbortException)
                return;
            $this->onError($e);
        }
    }

    /**
     * @param Form $form
     * @param ArrayHash $values
     */
    public function handleSuperGroupFormSuccess(Form $form, ArrayHash $values): void
    {
        try{
            $this->superGroupFunctionality->updatePermissions($values->id, $values->categories);
            $this->onSuccess();
        } catch (\Exception $e){
            //The exception that is thrown when user attempts to terminate the current presenter or application. This is special "silent exception" with no error message or code.
            if ($e instanceof AbortException)
                return;
            $this->onError($e);
        }
    }

    public function render(): void
    {
        if($this->super)
            $this->template->render(__DIR__ . DIRECTORY_SEPARATOR . 'superGroup.latte');
        else
            $this->template->render(__DIR__ . DIRECTORY_SEPARATOR . 'group.latte');
    }

    /**
     * @param Form $form
     * @param ArrayHash $values
     */
    public function handleCreateFormSuccess(Form $form, ArrayHash $values): void {}

    /**
     * @param Form $form
     * @param ArrayHash $values
     */
    public function handleEditFormSuccess(Form $form, ArrayHash $values): void {}
}