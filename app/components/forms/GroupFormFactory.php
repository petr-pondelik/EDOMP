<?php
/**
 * Created by PhpStorm.
 * User: wiedzmin
 * Date: 12.4.19
 * Time: 11:40
 */

namespace App\Components\Forms;

use App\Model\Managers\SuperGroupManager;
use App\Model\Repository\SuperGroupRepository;
use Nette\Application\UI\Form;
use Nette\Utils\ArrayHash;

/**
 * Class GroupFormFactory
 * @package App\Components\Forms
 */
class GroupFormFactory extends BaseFormControl
{
    /**
     * @var SuperGroupRepository
     */
    protected $superGroupRepository;

    /**
     * GroupFormFactory constructor.
     * @param SuperGroupRepository $superGroupRepository
     */
    public function __construct
    (
        SuperGroupRepository $superGroupRepository
    )
    {
        $this->superGroupRepository = $superGroupRepository;
    }

    /**
     * @return \Nette\Application\UI\Form
     * @throws \Exception
     */
    public function create()
    {
        $form = parent::create();

        $superGroupOptions = $this->superGroupRepository->findWithoutAdmin();

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