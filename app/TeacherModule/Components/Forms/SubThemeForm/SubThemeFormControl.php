<?php
/**
 * Created by PhpStorm.
 * User: wiedzmin
 * Date: 23.5.19
 * Time: 15:01
 */

namespace App\TeacherModule\Components\Forms\SubThemeForm;

use App\CoreModule\Arguments\ValidatorArgument;
use App\CoreModule\Components\Forms\EntityFormControl;
use App\CoreModule\Model\Persistent\Functionality\SubThemeFunctionality;
use App\CoreModule\Model\Persistent\Manager\ConstraintEntityManager;
use App\CoreModule\Model\Persistent\Repository\ThemeRepository;
use App\CoreModule\Services\Validator;
use Nette\Application\AbortException;
use Nette\Application\UI\Form;
use Nette\Utils\ArrayHash;

/**
 * Class SubThemeFormControl
 * @package App\TeacherModule\Components\Forms\SubThemeForm
 */
class SubThemeFormControl extends EntityFormControl
{
    /**
     * @var ThemeRepository
     */
    protected $themeRepository;

    /**
     * SubThemeFormControl constructor.
     * @param Validator $validator
     * @param ConstraintEntityManager $entityManager
     * @param SubThemeFunctionality $subThemeFunctionality
     * @param ThemeRepository $themeRepository
     */
    public function __construct
    (
        Validator $validator,
        ConstraintEntityManager $entityManager,
        SubThemeFunctionality $subThemeFunctionality,
        ThemeRepository $themeRepository
    )
    {
        parent::__construct($validator, $entityManager);
        $this->functionality = $subThemeFunctionality;
        $this->themeRepository = $themeRepository;
    }

    /**
     * @return Form
     * @throws \Exception
     */
    public function createComponentForm(): Form
    {
        $form = parent::createComponentForm();

        $themeOptions = $this->themeRepository->findAllowed($this->presenter->user);

        $form->addText('label', 'Název *')
            ->setHtmlAttribute('class', 'form-control')
            ->setHtmlAttribute('placeholder', 'Zadejte název podtématu.');

        $form->addSelect('theme', 'Téma *', $themeOptions)
            ->setPrompt('Zvolte téma')
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
        $validateFields['theme'] = new ValidatorArgument($values->theme, 'notEmpty');
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
    public function handleUpdateFormSuccess(Form $form, ArrayHash $values): void
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
        $this['form']['theme']->setDefaultValue($this->entity->getTheme()->getId());
    }
}