<?php
/**
 * Created by PhpStorm.
 * User: wiedzmin
 * Date: 26.5.19
 * Time: 11:47
 */

namespace App\StudentModule\Components\Forms\ProblemFilterForm;


use App\CoreModule\Components\Forms\FormControl;
use App\CoreModule\Model\Persistent\Repository\DifficultyRepository;
use App\CoreModule\Model\Persistent\Repository\SubThemeRepository;
use App\CoreModule\Services\Validator;
use Nette\Application\UI\Form;
use Nette\Utils\ArrayHash;

/**
 * Class ProblemFilterFormControl
 * @package App\StudentModule\Components\Forms\ProblemFilterForm
 */
class ProblemFilterFormControl extends FormControl
{
    /**
     * @var SubThemeRepository
     */
    protected $subThemeRepository;

    /**
     * @var DifficultyRepository
     */
    protected $difficultyRepository;

    /**
     * @var int
     */
    protected $themeId;

    /**
     * ProblemFilterFormControl constructor.
     * @param Validator $validator
     * @param SubThemeRepository $subThemeRepository
     * @param DifficultyRepository $difficultyRepository
     * @param int $themeId
     */
    public function __construct
    (
        Validator $validator,
        SubThemeRepository $subThemeRepository, DifficultyRepository $difficultyRepository,
        int $themeId
    )
    {
        parent::__construct($validator);
        $this->subThemeRepository = $subThemeRepository;
        $this->difficultyRepository = $difficultyRepository;
        $this->themeId = $themeId;
    }

    /**
     * @return Form
     * @throws \Exception
     */
    public function createComponentForm(): Form
    {
        $form = parent::createComponentForm();

        $form->getElementPrototype()->class('border-0 ajax');

        $difficultyOptions = $this->difficultyRepository->findAssoc([],"id");
        $themeOptions = $this->subThemeRepository->findAssoc(["theme" => $this->themeId], "id");

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

        $form['submit']->caption = 'Filtrovat';
        $form->onSuccess[] = [$this, 'handleFormSuccess'];

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
        $this->presenter->setFilters($values);
        $this->onSuccess();
    }
}