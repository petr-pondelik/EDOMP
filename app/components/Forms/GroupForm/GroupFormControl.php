<?php
/**
 * Created by PhpStorm.
 * User: wiedzmin
 * Date: 23.5.19
 * Time: 19:53
 */

namespace App\Components\Forms\GroupForm;


use App\Components\Forms\BaseFormControl;
use App\Model\Functionality\GroupFunctionality;
use App\Model\Repository\SuperGroupRepository;
use App\Service\ValidationService;
use Nette\Application\AbortException;
use Nette\Application\UI\Form;
use Nette\Utils\ArrayHash;

/**
 * Class GroupFormControl
 * @package App\Components\Forms\GroupForm
 */
class GroupFormControl extends BaseFormControl
{
    /**
     * @var SuperGroupRepository
     */
    protected $superGroupRepository;

    /**
     * GroupFormControl constructor.
     * @param ValidationService $validationService
     * @param GroupFunctionality $groupFunctionality
     * @param SuperGroupRepository $superGroupRepository
     * @param bool $edit
     */
    public function __construct
    (
        ValidationService $validationService,
        GroupFunctionality $groupFunctionality, SuperGroupRepository $superGroupRepository,
        bool $edit = false
    )
    {
        parent::__construct($validationService, $edit);
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

        $superGroupOptions = $this->superGroupRepository->findAllowed();

        $form->addText("label", "Název")
            ->setHtmlAttribute("class", "form-control");

        $form->addSelect("super_group_id", "Superskupina", $superGroupOptions)
            ->setHtmlAttribute("class", "form-control");

        $form->addSubmit("submit", "Vytvořit")
            ->setHtmlAttribute("class", "btn btn-primary");

        return $form;
    }

    /**
     * @param Form $form
     */
    public function handleFormValidate(Form $form): void
    {
        $values = $form->values;

        $validateFields["label"] = $values->label;

        $validationErrors = $this->validationService->validate($validateFields);

        if($validationErrors){
            foreach($validationErrors as $veKey => $errorGroup){
                foreach($errorGroup as $egKey => $error)
                    $form[$veKey]->addError($error);
            }
        }

        $this->redrawControl("labelErrorSnippet");
    }

    /**
     * @param Form $form
     * @param ArrayHash $values
     */
    public function handleCreateFormSuccess(Form $form, ArrayHash $values): void
    {
        try{
            $this->functionality->create($values);
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
    public function handleEditFormSuccess(Form $form, ArrayHash $values): void
    {
        try{
            $this->functionality->update($values->id_hidden, ArrayHash::from([
                "label" => $values->label,
                "super_group_id" => $values->super_group_id
            ]));
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
        if ($this->edit)
            $this->template->render(__DIR__ . DIRECTORY_SEPARATOR . "edit.latte");
        else
            $this->template->render(__DIR__ . DIRECTORY_SEPARATOR . "create.latte");
    }
}