<?php
/**
 * Created by PhpStorm.
 * User: wiedzmin
 * Date: 26.5.19
 * Time: 11:54
 */

namespace App\CoreModule\Components\Forms;

use App\CoreModule\Model\Persistent\Entity\BaseEntity;
use App\CoreModule\Model\Persistent\Manager\ConstraintEntityManager;
use App\CoreModule\Services\Validator;
use Nette\Application\UI\Form;
use Nette\Utils\ArrayHash;

/**
 * Class EntityFormControl
 * @package App\CoreModule\Components\Forms
 */
abstract class EntityFormControl extends FormControl
{
    /**
     * @var ConstraintEntityManager
     */
    protected $entityManager;

    /**
     * @var BaseEntity|null
     */
    protected $entity;

    /**
     * EntityFormControl constructor.
     * @param Validator $validator
     * @param ConstraintEntityManager $entityManager
     */
    public function __construct
    (
        Validator $validator,
        ConstraintEntityManager $entityManager
    )
    {
        parent::__construct($validator);
        $this->entityManager = $entityManager;
    }

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

    /**
     * @return string
     */
    public function getTemplateName(): string
    {
        if($this->isDefault()){
            return 'create';
        }
        return $this->getAction();
    }

    /**
     * @param Form $form
     * @param ArrayHash $values
     */
    abstract public function handleEditFormSuccess(Form $form, ArrayHash $values): void;

    abstract public function setDefaults(): void;
}