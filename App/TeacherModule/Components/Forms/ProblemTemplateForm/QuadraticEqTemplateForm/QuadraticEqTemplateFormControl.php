<?php
/**
 * Created by PhpStorm.
 * User: wiedzmin
 * Date: 5.7.19
 * Time: 22:46
 */

namespace App\TeacherModule\Components\Forms\ProblemTemplateForm\QuadraticEqTemplateForm;

use App\TeacherModule\Components\Forms\ProblemTemplateForm\ProblemTemplateFormControl;
use App\CoreModule\Helpers\ConstHelper;
use App\CoreModule\Model\Persistent\Functionality\ProblemTemplate\QuadraticEquationTemplateFunctionality;
use App\CoreModule\Model\Persistent\Manager\ConstraintEntityManager;
use App\CoreModule\Model\Persistent\Repository\DifficultyRepository;
use App\CoreModule\Model\Persistent\Repository\ProblemConditionRepository;
use App\CoreModule\Model\Persistent\Repository\ProblemConditionTypeRepository;
use App\CoreModule\Model\Persistent\Repository\ProblemTypeRepository;
use App\CoreModule\Model\Persistent\Repository\SubThemeRepository;
use App\TeacherModule\Services\ParameterParser;
use App\TeacherModule\Services\ProblemTemplateSession;
use App\CoreModule\Services\Validator;
use App\TeacherModule\Model\NonPersistent\Entity\ProblemTemplateNP;
use App\TeacherModule\Model\NonPersistent\Entity\QuadraticEquationTemplateNP;
use App\TeacherModule\Services\PluginContainer;
use Nette\Application\UI\Form;
use Nette\Utils\ArrayHash;

/**
 * Class QuadraticEqTemplateFormControl
 * @package App\TeacherModule\Components\Forms\ProblemTemplateForm\QuadraticEqTemplateForm
 */
final class QuadraticEqTemplateFormControl extends ProblemTemplateFormControl
{
    /**
     * @var array
     */
    protected $baseValidation = [
        [
            'field' => 'variable',
            'getter' => 'getVariable',
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
    protected $formName = 'QuadraticEqTemplateForm';

    /**
     * QuadraticEqTemplateFormControl constructor.
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
     * @param QuadraticEquationTemplateFunctionality $functionality
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
        QuadraticEquationTemplateFunctionality $functionality
    )
    {
        parent::__construct
        (
            $validator, $entityManager,
            $difficultyRepository, $problemTypeRepository, $subThemeRepository,
            $problemConditionTypeRepository, $problemConditionRepository,
            $pluginContainer,
            $parameterParser, $constHelper,
            $problemTemplateSession
        );
        $this->functionality = $functionality;
        $this->attachEntities($this->constHelper::QUADRATIC_EQ);
    }

    /**
     * @return Form
     * @throws \Exception
     */
    public function createComponentForm(): Form
    {
        $form = parent::createComponentForm();

        $form->addSelect('variable', 'Nezn??m?? *', [
            'x' => 'x',
            'y' => 'y',
            'z' => 'z'
        ])
            ->setHtmlAttribute('class', 'form-control')
            ->setHtmlAttribute('placeholder', 'Nezn??m?? ??ablony.')
            ->setHtmlId('variable');

        return $form;
    }

    /**
     * @param ArrayHash $values
     * @return ProblemTemplateNP
     */
    protected function createNonPersistentEntity(ArrayHash $values): ProblemTemplateNP
    {
        return new QuadraticEquationTemplateNP($values, $this->entity);
    }

    /**
     * @param ProblemTemplateNP $problemTemplate
     * @return ProblemTemplateNP|null
     */
    public function preprocess(ProblemTemplateNP $problemTemplate): ?ProblemTemplateNP
    {
        try{
            $preprocessed = $this->pluginContainer->getPlugin($this->problemType->getKeyLabel())->preprocess($problemTemplate);
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
        $this['form']['variable']->setDefaultValue($this->entity->getVariable());
    }
}