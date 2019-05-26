<?php
/**
 * Created by PhpStorm.
 * User: wiedzmin
 * Date: 26.5.19
 * Time: 11:47
 */

namespace App\Components\Forms\ProblemFilterForm;


use App\Components\Forms\FormControl;
use App\Model\Repository\DifficultyRepository;
use App\Model\Repository\SubCategoryRepository;
use App\Services\ValidationService;
use Nette\Application\UI\Form;
use Nette\Utils\ArrayHash;

/**
 * Class ProblemFilterFormControl
 * @package App\Components\Forms\ProblemFilterForm
 */
class ProblemFilterFormControl extends FormControl
{
    /**
     * @var SubCategoryRepository
     */
    protected $subCategoryRepository;

    /**
     * @var DifficultyRepository
     */
    protected $difficultyRepository;

    /**
     * @var int
     */
    protected $categoryId;

    /**
     * ProblemFilterFormControl constructor.
     * @param ValidationService $validationService
     * @param SubCategoryRepository $subCategoryRepository
     * @param DifficultyRepository $difficultyRepository
     * @param int $categoryId
     */
    public function __construct
    (
        ValidationService $validationService,
        SubCategoryRepository $subCategoryRepository, DifficultyRepository $difficultyRepository,
        int $categoryId
    )
    {
        parent::__construct($validationService);
        $this->subCategoryRepository = $subCategoryRepository;
        $this->difficultyRepository = $difficultyRepository;
        $this->categoryId = $categoryId;
    }

    /**
     * @return Form
     * @throws \Exception
     */
    public function createComponentForm(): Form
    {
        $form = parent::createComponentForm();

        $form->getElementPrototype()->class('border-light ajax');

        $difficultyOptions = $this->difficultyRepository->findAssoc([],"id");
        $themeOptions = $this->subCategoryRepository->findAssoc(["category" => $this->categoryId], "id");

        $form->addMultiSelect("difficulty", "Obtížnost", $difficultyOptions)
            ->setHtmlAttribute("class", "form-control selectpicker");

        $form->addMultiSelect("theme", "Témata", $themeOptions)
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

    public function clearFilters()
    {
        $this->presenter->filters = [];
        $this['form']["difficulty"]->setDefaultValue([]);
    }

    public function setFilters(ArrayHash $filters = null)
    {
        //Set filters values to the filter form
        if($filters === null){
            foreach($this->filters as $filterKey => $filter)
                $this["problemFilterForm"]['form'][$filterKey]->setDefaultValue($filter);
            return;
        }

        //Reset filters
        $this->clearFilters();

        //Set new filters
        foreach($filters as $filterKey => $filter)
            if( (is_array($filter) && count($filter) > 0) || !is_array($filter) )
                $this->filters[$filterKey] = $filter;
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
        // TODO: Implement handleFormSuccess() method.
    }

    public function action(): void
    {

    }

    public function render(): void
    {
        $this->template->render(__DIR__ . '/templates/default.latte');
    }
}