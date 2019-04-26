<?php
/**
 * Created by PhpStorm.
 * User: wiedzmin
 * Date: 22.4.19
 * Time: 10:10
 */

namespace App\Components\Forms;

use App\Model\Managers\CategoryManager;

/**
 * Class PermissionFormFactory
 * @package App\Components\Forms
 */
class PermissionFormFactory extends BaseForm
{
    /**
     * @var CategoryManager
     */
    protected $categoryManager;

    /**
     * PermissionFormFactory constructor.
     * @param CategoryManager $categoryManager
     */
    public function __construct
    (
        CategoryManager $categoryManager
    )
    {
        $this->categoryManager = $categoryManager;
    }

    public function create()
    {
        $form = parent::create();

        $categoryOptions = $this->categoryManager->getAll("ASC");

        $form->addHidden("item_id");

        $form->addMultiSelect("categories", "Kategorie", $categoryOptions)
            ->setHtmlAttribute("class", "form-control selectpicker");

        $form->addSubmit("submit", "Uložit")
            ->setHtmlAttribute("class", "btn btn-primary btn-sm");

        return $form;
    }

}