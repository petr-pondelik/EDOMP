<?php
/**
 * Created by PhpStorm.
 * User: wiedzmin
 * Date: 8.4.19
 * Time: 17:28
 */

namespace App\Components\Forms;

/**
 * Class CategoryFormFactory
 * @package App\Components\Forms
 */
class CategoryFormFactory extends BaseForm
{
    /**
     * @return \Nette\Application\UI\Form
     */
    public function create()
    {
        $form = parent::create();

        $form->addText('label', 'Název')
            ->setHtmlAttribute('class', 'form-control');

        $form->addSubmit('submit', 'Vytvořit')
            ->setHtmlAttribute('class', 'btn btn-primary');

        return $form;
    }
}