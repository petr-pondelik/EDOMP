<?php
/**
 * Created by PhpStorm.
 * User: wiedzmin
 * Date: 24.5.19
 * Time: 14:50
 */

namespace App\TeacherModule\Components\Forms\ProblemFinalForm;


use App\CoreModule\Arguments\ValidatorArgument;
use App\CoreModule\Components\Forms\EntityFormControl;
use App\CoreModule\Helpers\ConstHelper;
use App\CoreModule\Model\Persistent\Functionality\ProblemFinal\ProblemFinalFunctionality;
use App\CoreModule\Model\Persistent\Manager\ConstraintEntityManager;
use App\CoreModule\Model\Persistent\Repository\DifficultyRepository;
use App\CoreModule\Model\Persistent\Repository\ProblemConditionRepository;
use App\CoreModule\Model\Persistent\Repository\ProblemConditionTypeRepository;
use App\CoreModule\Model\Persistent\Repository\ProblemTypeRepository;
use App\CoreModule\Model\Persistent\Repository\SubThemeRepository;
use App\CoreModule\Services\Validator;
use Nette\Application\AbortException;
use Nette\Application\UI\Form;
use Nette\Utils\ArrayHash;

/**
 * Class ProblemFinalFormControl
 * @package App\TeacherModule\Components\Forms\ProblemFinalForm
 */
class ProblemFinalFormControl extends EntityFormControl
{
    /**
     * @var DifficultyRepository
     */
    protected $difficultyRepository;

    /**
     * @var ProblemTypeRepository
     */
    protected $problemTypeRepository;

    /**
     * @var SubThemeRepository
     */
    protected $subThemeRepository;

    /**
     * @var ProblemConditionTypeRepository
     */
    protected $problemConditionTypeRepository;

    /**
     * @var ProblemConditionRepository
     */
    protected $problemConditionRepository;

    /**
     * @var ConstHelper
     */
    protected $constHelper;

    /**
     * ProblemFinalFormControl constructor.
     * @param Validator $validator
     * @param ConstraintEntityManager $entityManager
     * @param ProblemFinalFunctionality $problemFinalFunctionality
     * @param DifficultyRepository $difficultyRepository
     * @param ProblemTypeRepository $problemTypeRepository
     * @param SubThemeRepository $subThemeRepository
     * @param ProblemConditionTypeRepository $problemConditionTypeRepository
     * @param ProblemConditionRepository $problemConditionRepository
     * @param ConstHelper $constHelper
     */
    public function __construct
    (
        Validator $validator,
        ConstraintEntityManager $entityManager,
        ProblemFinalFunctionality $problemFinalFunctionality,
        DifficultyRepository $difficultyRepository,
        ProblemTypeRepository $problemTypeRepository,
        SubThemeRepository $subThemeRepository,
        ProblemConditionTypeRepository $problemConditionTypeRepository,
        ProblemConditionRepository $problemConditionRepository,
        ConstHelper $constHelper
    )
    {
        parent::__construct($validator, $entityManager);
        $this->functionality = $problemFinalFunctionality;
        $this->difficultyRepository = $difficultyRepository;
        $this->problemTypeRepository = $problemTypeRepository;
        $this->subThemeRepository = $subThemeRepository;
        $this->problemConditionTypeRepository = $problemConditionTypeRepository;
        $this->problemConditionRepository = $problemConditionRepository;
        $this->constHelper = $constHelper;
    }

    /**
     * @return Form
     * @throws \Exception
     */
    public function createComponentForm(): Form
    {
        $form = parent::createComponentForm();

        $difficulties = $this->difficultyRepository->findAssoc([], 'id');
        $subThemes = $this->subThemeRepository->findAssoc([], 'id');

        $form->addHidden('is_generated_hidden');

        $form->addSelect('subTheme', 'Podtéma *', $subThemes)
            ->setPrompt('Zvolte podtéma')
            ->setHtmlAttribute('class', 'form-control');

        $form->addSelect('studentVisible', 'Zobrazit ve cvičebnici *', [
            1 => 'Ano',
            0 => 'Ne'
        ])
            ->setHtmlAttribute('class', 'form-control');

        $form->addTextArea('textBefore', 'Úvod zadání')
            ->setHtmlAttribute('class', 'form-control')
            ->setHtmlAttribute('placeholder', 'Úvodní text zadání.')
            ->setHtmlId('before');

        $form->addTextArea('body', 'Úloha *')
            ->setHtmlAttribute('class', 'form-control')
            ->setHtmlAttribute('placeholder','Sem patří samotné zadání úlohy.')
            ->setHtmlId('structure');

        $form->addText('variable', 'Neznámá')
            ->setHtmlAttribute('class', 'form-control')
            ->setHtmlId('variable');

        $form->addTextArea('textAfter', 'Dodatek k zadání')
            ->setHtmlAttribute('class', 'form-control')
            ->setHtmlAttribute('placeholder', 'Dodatečný text k zadání.')
            ->setHtmlId('after');

        $form->addSelect('difficulty', 'Obtížnost *', $difficulties)
            ->setPrompt('Zvolte obtížnost')
            ->setHtmlAttribute('class', 'form-control')
            ->setHtmlId('difficulty');

        $form->addTextArea('result', 'Výsledek')
            ->setHtmlAttribute('placeholder', 'Výsledek úlohy.')
            ->setHtmlAttribute('class', 'form-control');

        return $form;
    }

    /**
     * @param Form $form
     * @throws \App\CoreModule\Exceptions\ValidatorException
     */
    public function handleFormValidate(Form $form): void
    {
        $values = $form->getValues();
        $validateFields['body'] = new ValidatorArgument($values->body, 'notEmpty');
        $validateFields['difficulty'] = new ValidatorArgument($values->difficulty, 'notEmpty', 'difficulty');
        $validateFields['subTheme'] = new ValidatorArgument($values->subTheme, 'notEmpty', 'subTheme');
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
        try{
            bdump($values);
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
        try{
            $this->functionality->update($this->entity->getId(), $values);
            $this->onSuccess();
        } catch (\Exception $e) {
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
        $this['form']['is_generated_hidden']->setDefaultValue($this->entity->isGenerated());
        $this['form']['textBefore']->setDefaultValue($this->entity->getTextBefore());
        $this['form']['textAfter']->setDefaultValue($this->entity->getTextAfter());
        $this['form']['result']->setDefaultValue($this->entity->getResult());
        $this['form']['difficulty']->setDefaultValue($this->entity->getDifficulty()->getId());
        $this['form']['subTheme']->setDefaultValue($this->entity->getSubTheme()->getId());
        $this['form']['studentVisible']->setDefaultValue((int) $this->entity->isStudentVisible());

        if($this->entity->isGenerated()){
            $this['form']['body']->setDisabled();
        }

        $this['form']['body']->setDefaultValue($this->entity->getBody());
    }
}