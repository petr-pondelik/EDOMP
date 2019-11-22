<?php
/**
 * Created by PhpStorm.
 * User: wiedzmin
 * Date: 5.7.19
 * Time: 22:58
 */

namespace App\TeacherModule\Components\Forms\ProblemTemplateForm\ArithmeticSeqTemplateForm;


use App\TeacherModule\Components\Forms\ProblemTemplateForm\ProblemTemplateFormControl;
use App\TeacherModule\Exceptions\EquationException;
use App\CoreModule\Helpers\ConstHelper;
use App\CoreModule\Model\Persistent\Functionality\ProblemTemplate\ArithmeticSequenceTemplateFunctionality;
use App\CoreModule\Model\Persistent\Manager\ConstraintEntityManager;
use App\CoreModule\Model\Persistent\Repository\DifficultyRepository;
use App\CoreModule\Model\Persistent\Repository\ProblemConditionRepository;
use App\CoreModule\Model\Persistent\Repository\ProblemConditionTypeRepository;
use App\CoreModule\Model\Persistent\Repository\ProblemTypeRepository;
use App\CoreModule\Model\Persistent\Repository\SubThemeRepository;
use App\TeacherModule\Services\ParameterParser;
use App\TeacherModule\Services\ProblemTemplateSession;
use App\CoreModule\Services\Validator;
use App\TeacherModule\Model\NonPersistent\Entity\ArithmeticSequenceTemplateNP;
use App\TeacherModule\Model\NonPersistent\Entity\ProblemTemplateNP;
use App\TeacherModule\Services\PluginContainer;
use Nette\Application\UI\Form;
use Nette\Utils\ArrayHash;

/**
 * Class ArithmeticSeqTemplateFormControl
 * @package App\TeacherModule\Components\Forms\ProblemTemplateForm\ArithmeticSeqTemplateForm
 */
class ArithmeticSeqTemplateFormControl extends ProblemTemplateFormControl
{
    /**
     * @var array
     */
    protected $baseValidation = [
        [
            'field' => 'indexVariable',
            'getter' => 'getIndexVariable',
            'validation' => 'variable'
        ],
        [
            'field' => 'subTheme',
            'getter' => 'getSubTheme',
            'validation' => 'notEmpty'
        ],
        [
            'field' => 'difficulty',
            'getter' => 'getDifficulty',
            'validation' => 'notEmpty'
        ],
        [
            'field' => 'firstN',
            'getter' => 'getFirstN',
            'validation' => 'notEmptyPositive'
        ]
    ];

    /**
     * @var array
     */
    protected $baseConditionValidation = [
        [
            'field' => 'variable',
            'getter' => 'getVariable',
            'validation' => 'variable'
        ]
    ];

    /**
     * @var string
     */
    protected $formName = 'ArithmeticSeqTemplateForm';

    /**
     * ArithmeticSeqTemplateFormControl constructor.
     * @param Validator $validator
     * @param ConstraintEntityManager $entityManager
     * @param DifficultyRepository $difficultyRepository
     * @param ProblemTypeRepository $problemTypeRepository
     * @param SubThemeRepository $subThemeRepository
     * @param ProblemConditionTypeRepository $problemConditionTypeRepository
     * @param ProblemConditionRepository $problemConditionRepository
     * @param PluginContainer $pluginContainer
     * @param ParameterParser $parameterParser
     * @param ConstHelper $constHelper
     * @param ProblemTemplateSession $problemTemplateSession
     * @param ArithmeticSequenceTemplateFunctionality $functionality
     */
    public function __construct
    (
        Validator $validator,
        ConstraintEntityManager $entityManager,
        DifficultyRepository $difficultyRepository,
        ProblemTypeRepository $problemTypeRepository,
        SubThemeRepository $subThemeRepository,
        ProblemConditionTypeRepository $problemConditionTypeRepository,
        ProblemConditionRepository $problemConditionRepository,
        PluginContainer $pluginContainer,
        ParameterParser $parameterParser,
        ConstHelper $constHelper,
        ProblemTemplateSession $problemTemplateSession,
        ArithmeticSequenceTemplateFunctionality $functionality
    )
    {
        parent::__construct
        (
            $validator, $entityManager,
            $difficultyRepository, $problemTypeRepository, $subThemeRepository,
            $problemConditionTypeRepository, $problemConditionRepository,
            $pluginContainer,
            $parameterParser,
            $constHelper,
            $problemTemplateSession
        );
        $this->functionality = $functionality;
        $this->attachEntities($this->constHelper::ARITHMETIC_SEQ);
    }

    /**
     * @return Form
     * @throws \Exception
     */
    public function createComponentForm(): Form
    {
        $form = parent::createComponentForm();

        $form->addText('indexVariable', 'Index *')
            ->setHtmlAttribute('class', 'form-control')
            ->setHtmlAttribute('placeholder', 'Zadejte index šablony posloupnosti.')
            ->setHtmlId('variable');

        $form->addInteger('firstN', 'Počet prvních členů *')
            ->setHtmlAttribute('class', 'form-control')
            ->setHtmlAttribute('placeholder', 'Zadejte počet zkoumaných prvních členů.')
            ->setHtmlId('first-n');

        return $form;
    }

    /**
     * @param ArrayHash $values
     * @return ArithmeticSequenceTemplateNP|mixed
     */
    protected function createNonPersistentEntity(ArrayHash $values): ProblemTemplateNP
    {
        return new ArithmeticSequenceTemplateNP($values);
    }

    /**
     * @param ProblemTemplateNP $problemTemplate
     * @return ProblemTemplateNP|null
     */
    public function preprocess(ProblemTemplateNP $problemTemplate): ?ProblemTemplateNP
    {
        try{
            $preprocessed = $this->pluginContainer->getPlugin($this->problemType->getKeyLabel())->preprocess($problemTemplate);
        } catch (EquationException $e){
            $this['form']['body']->addError('Zadaný výraz není validním předpisem posloupnosti.');
            return null;
        } catch (\Exception $e){
            $this['form']['body']->addError($e->getMessage());
            return null;
        }
        return $preprocessed;
    }

    public function setDefaults(): void
    {
        if(!$this->isUpdate()){
            return;
        }
        parent::setDefaults();
        $this['form']['indexVariable']->setDefaultValue($this->entity->getIndexVariable());
        $this['form']['firstN']->setDefaultValue($this->entity->getFirstN());
    }
}