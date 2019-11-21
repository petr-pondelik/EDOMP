<?php
/**
 * Created by PhpStorm.
 * User: wiedzmin
 * Date: 23.5.19
 * Time: 15:01
 */

namespace App\TeacherModule\Components\Forms\SubCategoryForm;

use App\CoreModule\Arguments\ValidatorArgument;
use App\CoreModule\Components\Forms\EntityFormControl;
use App\CoreModule\Model\Persistent\Functionality\SubCategoryFunctionality;
use App\CoreModule\Model\Persistent\Manager\ConstraintEntityManager;
use App\CoreModule\Model\Persistent\Repository\CategoryRepository;
use App\CoreModule\Services\Validator;
use Nette\Application\AbortException;
use Nette\Application\UI\Form;
use Nette\Utils\ArrayHash;

/**
 * Class SubCategoryFormControl
 * @package App\TeacherModule\Components\Forms\SubCategoryForm
 */
class SubCategoryFormControl extends EntityFormControl
{
    /**
     * @var CategoryRepository
     */
    protected $categoryRepository;

    /**
     * SubCategoryFormControl constructor.
     * @param Validator $validator
     * @param ConstraintEntityManager $entityManager
     * @param SubCategoryFunctionality $subCategoryFunctionality
     * @param CategoryRepository $categoryRepository
     */
    public function __construct
    (
        Validator $validator,
        ConstraintEntityManager $entityManager,
        SubCategoryFunctionality $subCategoryFunctionality,
        CategoryRepository $categoryRepository
    )
    {
        parent::__construct($validator, $entityManager);
        $this->functionality = $subCategoryFunctionality;
        $this->categoryRepository = $categoryRepository;
    }

    /**
     * @return Form
     * @throws \Exception
     */
    public function createComponentForm(): Form
    {
        $form = parent::createComponentForm();

        $categoryOptions = $this->categoryRepository->findAllowed($this->presenter->user);

        $form->addText('label', 'Název *')
            ->setHtmlAttribute('class', 'form-control')
            ->setHtmlAttribute('placeholder', 'Zadejte název podkategorie.');

        $form->addSelect('category', 'Kategorie *', $categoryOptions)
            ->setPrompt('Zvolte kategorii')
            ->setHtmlAttribute('class', 'form-control');

        return $form;
    }

    /**
     * @param Form $form
     * @throws \App\CoreModule\Exceptions\ValidatorException
     */
    public function handleFormValidate(Form $form): void
    {
        $values = $form->values;
        $validateFields['label'] = new ValidatorArgument($values->label, 'stringNotEmpty');
        $validateFields['category'] = new ValidatorArgument($values->category, 'notEmpty');
        $this->validator->validate($form, $validateFields);
        $this->redrawErrors();
        $this->redrawFlashes();
    }

    /**
     * @param Form $form
     * @param ArrayHash $values
     */
    public function handleFormSuccess(Form $form, ArrayHash $values): void
    {
        try {
            $values->userId = $this->presenter->user->id;
            $this->functionality->create($values);
            $this->onSuccess();
        } catch (\Exception $e) {
            // The exception that is thrown when user attempts to terminate the current presenter or application. This is special "silent exception" with no error message or code.
            if ($e instanceof AbortException) {
                return;
            }
            $this->onError($e);
        }
    }

    /**
     * @param Form $form
     * @param ArrayHash $values
     */
    public function handleEditFormSuccess(Form $form, ArrayHash $values): void
    {
        try {
            $this->functionality->update($this->entity->getId(), $values);
            $this->onSuccess();
        } catch (\Exception $e) {
            //The exception that is thrown when user attempts to terminate the current presenter or application. This is special "silent exception" with no error message or code.
            if ($e instanceof AbortException) {
                return;
            }
            $this->onError($e);
        }
    }

    public function setDefaults(): void
    {
        if(!$this->entity){
            return;
        }
        $this['form']['id']->setDefaultValue($this->entity->getId());
        $this['form']['label']->setDefaultValue($this->entity->getLabel());
        $this['form']['category']->setDefaultValue($this->entity->getCategory()->getId());
    }
}