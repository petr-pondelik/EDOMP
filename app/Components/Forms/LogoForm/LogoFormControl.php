<?php
/**
 * Created by PhpStorm.
 * User: wiedzmin
 * Date: 24.5.19
 * Time: 11:54
 */

namespace App\Components\Forms\LogoForm;


use App\Arguments\ValidatorArgument;
use App\Components\Forms\EntityFormControl;
use App\Components\LogoView\ILogoViewFactory;
use App\Model\Persistent\Functionality\LogoFunctionality;
use App\Model\Persistent\Manager\ConstraintEntityManager;
use App\Model\Persistent\Repository\LogoRepository;
use App\Services\FileService;
use App\Services\Validator;
use Nette\Application\AbortException;
use Nette\Application\UI\Form;
use Nette\Utils\ArrayHash;

/**
 * Class LogoFormControl
 * @package App\Components\Forms\LogoForm
 */
class LogoFormControl extends EntityFormControl
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
        if($this->isUpdate()){
            $this->addComponent($this->logoViewFactory->create(), 'logoView');
        }
    }

    public function initComponents(): void
    {
        parent::initComponents();
        if($this->isUpdate()){
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

        if($this->isUpdate()){
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
     */
    public function handleFormValidate(Form $form): void
    {
        $values = $form->values;
        $validateFields['label'] = new ValidatorArgument($values->label, 'stringNotEmpty');
        if($this->isUpdate()){
            if($values->edit_logo){
                $validateFields['logo'] = new ValidatorArgument($values->logo, 'notEmpty');
            }
        }
        else{
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
        if($values->logo) {
            try{
                $this->fileService->finalStore($values->logo);
                $this->functionality->update($values->logo, ArrayHash::from([ 'label' => $values->label ]));
                $this->onSuccess();
            } catch (\Exception $e){
                // The exception that is thrown when user attempts to terminate the current presenter or application. This is special "silent exception" with no error message or code.
                if ($e instanceof AbortException){
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
    public function handleEditFormSuccess(Form $form, ArrayHash $values): void
    {
        bdump('HANDLE EDIT FORM SUCCESS');
        bdump($values);
        try{
            if($values->edit_logo && $values->logo){
                $this->fileService->finalStore($values->logo);
            }
            $data['label'] = $values->label;
            $this->functionality->update($this->entity->getId(), ArrayHash::from($data));
            $this->onSuccess();
        } catch (\Exception $e){
            bdump($e);
            // The exception that is thrown when user attempts to terminate the current presenter or application. This is special "silent exception" with no error message or code.
            if ($e instanceof AbortException){
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