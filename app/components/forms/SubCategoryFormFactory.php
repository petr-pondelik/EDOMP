<?php
/**
 * Created by PhpStorm.
 * User: wiedzmin
 * Date: 11.4.19
 * Time: 12:43
 */

namespace App\Components\Forms;

use App\Model\Repository\CategoryRepository;

/**
 * Class SubCategoryFormFactory
 * @package Nette\Forms
 */
class SubCategoryFormFactory extends BaseForm
{
    /**
     * @var CategoryRepository
     */
    protected $categoryRepository;

    /**
     * SubCategoryFormFactory constructor.
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
    public function create()
    {
        $form = parent::create();

        $categoryOptions = $this->categoryRepository->findAssoc([], "id");

        bdump($categoryOptions);

        $form->addText("label", "Název")
            ->setHtmlAttribute("class", "form-control");

        $form->addSelect("category", "Kategorie", $categoryOptions)
            ->setHtmlAttribute("class", "form-control");

        $form->addSubmit("submit", "Vytvořit")
            ->setHtmlAttribute("class", "btn btn-primary");

        return $form;
    }
}