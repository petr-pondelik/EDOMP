<?php
/**
 * Created by PhpStorm.
 * User: wiedzmin
 * Date: 26.5.19
 * Time: 11:54
 */

namespace App\Components\Forms;

use App\Model\Persistent\Entity\BaseEntity;
use Nette\Application\UI\Form;
use Nette\Utils\ArrayHash;
use ReflectionClass;

/**
 * Class EntityFormControl
 * @package App\Components\Forms
 */
abstract class EntityFormControl extends FormControl
{
    /**
     * @var BaseEntity|null
     */
    protected $entity;

    /**
     * @return Form
     */
    public function createComponentForm(): Form
    {
        $form = parent::createComponentForm();

        if ($this->isUpdate()) {
            $form->addInteger('id', 'ID')
                ->setHtmlAttribute('class', 'form-control')
                ->setDisabled();
            $form->onSuccess[] = [$this, 'handleEditFormSuccess'];
        }
        else{
            $form->onSuccess[] = [$this, 'handleFormSuccess'];
        }

        return $form;
    }

//    /**
//     * @return string
//     */
//    public function getTemplateName(): string
//    {
//        return $this->isUpdate() ? 'update' : 'create';
//    }

    /**
     * @param Form $form
     * @param ArrayHash $values
     */
    abstract public function handleEditFormSuccess(Form $form, ArrayHash $values): void;

    abstract public function setDefaults(): void;

    /**
     * @param BaseEntity $entity
     */
    public function setEntity(BaseEntity $entity): void
    {
        $this->entity = $entity;
        $this->template->entity = $entity;
    }

    /**
     * @return BaseEntity|null
     */
    public function getEntity(): ?BaseEntity
    {
        return $this->entity;
    }
}