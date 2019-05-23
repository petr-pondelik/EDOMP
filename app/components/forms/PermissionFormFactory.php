<?php
/**
 * Created by PhpStorm.
 * User: wiedzmin
 * Date: 22.4.19
 * Time: 10:10
 */

namespace App\Components\Forms;

use App\Model\Repository\CategoryRepository;
use Nette\Application\UI\Form;
use Nette\Utils\ArrayHash;

/**
 * Class PermissionFormFactory
 * @package App\Components\Forms
 */
class PermissionFormFactory extends BaseFormControl
{
    /**
     * @var CategoryRepository
     */
    protected $categoryRepository;

    /**
     * PermissionFormFactory constructor.
     * @param CategoryRepository $categoryRepository
     */
    public function __construct
    (
        CategoryRepository $categoryRepository
    )
    {
        $this->categoryRepository = $categoryRepository;
    }

    /**
     * @return \Nette\Application\UI\Form
     * @throws \Exception
     */
    public function create(): Form
    {
        $form = parent::create();

        $categoryOptions = $this->categoryRepository->findAssoc([], "id");

        $form->addHidden("id");

        $form->addMultiSelect("categories", "Kategorie", $categoryOptions)
            ->setHtmlAttribute("class", "form-control selectpicker");

        $form->addSubmit("submit", "Uložit")
            ->setHtmlAttribute("class", "btn btn-primary btn-sm");

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