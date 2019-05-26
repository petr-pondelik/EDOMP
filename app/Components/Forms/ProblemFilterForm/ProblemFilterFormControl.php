<?php
/**
 * Created by PhpStorm.
 * User: wiedzmin
 * Date: 26.5.19
 * Time: 11:47
 */

namespace App\Components\Forms\ProblemFilterForm;


use App\Components\Forms\BaseFormControl;
use App\Services\ValidationService;
use Nette\Application\UI\Form;
use Nette\Utils\ArrayHash;

/**
 * Class ProblemFilterFormControl
 * @package App\Components\Forms\ProblemFilterForm
 */
class ProblemFilterFormControl extends BaseFormControl
{
    public function __construct(ValidationService $validationService, bool $edit = false, bool $super = false)
    {
        parent::__construct($validationService, $edit, $super);
    }

    public function createComponentForm(): Form
    {
        $form = parent::createComponentForm();

        $form->getElementPrototype()->class('border-light ajax');

        $difficultyOptions = $this->difficultyRepository->findAssoc([],"id");

        $form->addMultiSelect("difficulty", "Obtížnost", $difficultyOptions)
            ->setHtmlAttribute("class", "form-control selectpicker");

        $form->addMultiSelect("result", "S řešením", [
            0 => "Ano",
            1 => "Ne"
        ])
            ->setHtmlAttribute("class", "form-control selectpicker");

        $form->addSelect("sort_by_difficulty", "Řadit dle obtížnost", [
            0 => "Neřadit",
            1 => "Vzestupně",
            2 => "Sestupně"
        ])
            ->setHtmlAttribute("class", "form-control");

        return $form;
    }

    /**
     * @param Form $form
     */
    public function handleFormValidate(Form $form): void
    {
        // TODO: Implement handleFormValidate() method.
    }

    /**
     * @param Form $form
     * @param ArrayHash $values
     */
    public function handleCreateFormSuccess(Form $form, ArrayHash $values): void
    {
        // TODO: Implement handleCreateFormSuccess() method.
    }

    /**
     * @param Form $form
     * @param ArrayHash $values
     */
    public function handleEditFormSuccess(Form $form, ArrayHash $values): void
    {
        // TODO: Implement handleEditFormSuccess() method.
    }

    public function render(): void
    {
        // TODO: Implement render() method.
    }
}