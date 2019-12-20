<?php
/**
 * Created by PhpStorm.
 * User: wiedzmin
 * Date: 24.5.19
 * Time: 11:54
 */

namespace App\TeacherModule\Components\Forms\LogoForm;


use App\CoreModule\Arguments\ValidatorArgument;
use App\CoreModule\Components\Forms\EntityFormControl;
use App\TeacherModule\Components\LogoView\ILogoViewFactory;
use App\CoreModule\Model\Persistent\Functionality\LogoFunctionality;
use App\CoreModule\Model\Persistent\Manager\ConstraintEntityManager;
use App\CoreModule\Model\Persistent\Repository\LogoRepository;
use App\CoreModule\Services\FileService;
use App\CoreModule\Services\Validator;
use Nette\Application\AbortException;
use Nette\Application\UI\Form;
use Nette\Utils\ArrayHash;

/**
 * Class LogoFormControl
 * @package App\TeacherModule\Components\Forms\LogoForm
 */
final class LogoFormControl extends EntityFormControl
{
    /**
     * @var LogoRepository
     */
    protected $repository;

    /**
     * @var FileService
     */
    protected $fileService;

    /**
     * @var ILogoViewFactory
     */
    protected $logoViewFactory;

    /**
     * LogoFormControl constructor.
     * @param Validator $validator
     * @param ConstraintEntityManager $entityManager
     * @param LogoFunctionality $logoFunctionality
     * @param FileService $fileService
     * @param ILogoViewFactory $logoViewFactory
     */
    public function __construct
    (
        Validator $validator,
        ConstraintEntityManager $entityManager,
        LogoFunctionality $logoFunctionality,
        FileService $fileService,
        ILogoViewFactory $logoViewFactory
    )
    {
        parent::__construct($validator, $entityManager);
        $this->functionality = $logoFunctionality;
        $this->fileService = $fileService;
        $this->logoViewFactory = $logoViewFactory;
    }

    /**
     * @param array $params
     * @throws \Nette\Application\BadRequestException
     */
    public function loadState($params): void
    {
        parent::loadState($params);
        if ($this->isUpdate()) {
            $this->addComponent($this->logoViewFactory->create(), 'logoView');
        }
    }

    /**
     * @param iterable|null $args
     */
    public function initComponents(iterable $args = null): void
    {
        if ($this->isUpdate()) {
            $this['logoView']->setLogo($this->entity);
        }
    }

    /**
     * @return Form
     */
    public function createComponentForm(): Form
    {
        $form = parent::createComponentForm();

        $form->addText('label', 'Název *')
            ->setHtmlAttribute('class', 'form-control')
            ->setHtmlAttribute('placeholder', 'Zadejte název loga.');

        $form->addText('logo', 'Soubor *')
            ->setHtmlAttribute('class', 'file-pond-input');

        if ($this->isUpdate()) {
            $form->addSelect('edit_logo', 'Editovat soubor', [
                0 => 'Ne',
                1 => 'Ano'
            ])
                ->setHtmlAttribute('class', 'form-control mb-3');
        }

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
        if ($this->isUpdate()) {
            if ($values->edit_logo) {
                $validateFields['logo'] = new ValidatorArgument($values->logo, 'notEmpty');
            }
        } else {
            $validateFields['logo'] = new ValidatorArgument($values->logo, 'notEmpty');
        }
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
        if ($values->logo) {
            try {
                $this->fileService->finalStore($values->logo);
                $values->createdBy = $this->presenter->user->getId();
                $this->functionality->update($values->logo, ArrayHash::from([
                    'label' => $values->label,
                    'createdBy' => $values->createdBy
                ]));
                $this->onSuccess();
            } catch (\Exception $e) {
                // The exception that is thrown when user attempts to terminate the current presenter or application. This is special "silent exception" with no error message or code.
                if ($e instanceof AbortException) {
                    return;
                }
                $this->onError($e);
            }
        }
    }

    /**
     * @param Form $form
     * @param ArrayHash $values
     */
    public function handleUpdateFormSuccess(Form $form, ArrayHash $values): void
    {
        bdump('HANDLE EDIT FORM SUCCESS');
        bdump($values);
        try {
            if ($values->edit_logo && $values->logo) {
                $this->fileService->finalStore($values->logo);
            }
            $data['label'] = $values->label;
            $this->functionality->update($this->entity->getId(), ArrayHash::from($data));
            $this->onSuccess();
        } catch (\Exception $e) {
            bdump($e);
            // The exception that is thrown when user attempts to terminate the current presenter or application. This is special "silent exception" with no error message or code.
            if ($e instanceof AbortException) {
                return;
            }
            $this->onError($e);
        }
    }

    public function setDefaults(): void
    {
        $this['form']['id']->setDefaultValue($this->entity->getId());
        $this['form']['label']->setDefaultValue($this->entity->getLabel());
    }
}