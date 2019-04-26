<?php
/**
 * Created by PhpStorm.
 * User: wiedzmin
 * Date: 11.4.19
 * Time: 12:43
 */

namespace App\Components\Forms;

use App\Model\Managers\CategoryManager;

/**
 * Class SubCategoryFormFactory
 * @package Nette\Forms
 */
class SubCategoryFormFactory extends BaseForm
{
    /**
     * @var CategoryManager
     */
    protected $categoryManager;

    /**
     * SubCategoryFormFactory constructor.
     * @param CategoryManager $categoryManager
     */
    public function __construct
    (
        CategoryManager $categoryManager
    )
    {
        $this->categoryManager = $categoryManager;
    }

    /**
     * @return \Nette\Application\UI\Form
     */
    public function create()
    {
        $form = parent::create();

        $categoryOptions = $this->categoryManager->getAllPairs("ASC");

        $form->addText("label", "Název")
            ->setHtmlAttribute("class", "form-control");

        $form->addSelect("category_id", "Kategorie", $categoryOptions)
            ->setHtmlAttribute("class", "form-control");

        $form->addSubmit("submit", "Vytvořit")
            ->setHtmlAttribute("class", "btn btn-primary");

        return $form;
    }
}