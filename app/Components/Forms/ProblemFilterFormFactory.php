<?php
/**
 * Created by PhpStorm.
 * User: wiedzmin
 * Date: 16.4.19
 * Time: 9:11
 */

namespace App\Components\Forms;

use App\Model\Persistent\Managers\DifficultyManager;
use App\Model\Persistent\Repository\DifficultyRepository;
use Nette\Application\UI\Form;
use Nette\Forms\Controls\Checkbox;
use Nette\Utils\ArrayHash;

/**
 * Class ProblemFilterFormFactory
 * @package App\Components\Forms
 */
class ProblemFilterFormFactory extends FormControl
{
    /**
     * @var DifficultyRepository
     */
    protected $difficultyRepository;

    /**
     * ProblemFilterFormFactory constructor.
     * @param DifficultyRepository $difficultyRepository
     */
    public function __construct
    (
        DifficultyRepository $difficultyRepository
    )
    {
        $this->difficultyRepository = $difficultyRepository;
    }

    /**
     * @return Form
     * @throws \Exception
     */
    public function create(): Form
    {
        $form = parent::create();

        $form->getElementPrototype()->class('border-light ajax');

        $difficultyOptions = $this->difficultyRepository->findAssoc([],"id");

        //bdump($difficultyOptions);

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

        $form->addSubmit("submit", "Filtrovat")
            ->setHtmlAttribute("class", "btn btn-sm btn-success");

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

    /**
     * @param Form $form
     * @param ArrayHash $values
     */
    public function handleFormSuccess(Form $form, ArrayHash $values): void
    {
        // TODO: Implement handleFormSuccess() method.
    }
}