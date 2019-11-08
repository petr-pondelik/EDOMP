<?php
/**
 * Created by PhpStorm.
 * User: wiedzmin
 * Date: 5.7.19
 * Time: 21:59
 */

namespace App\TeacherModule\Components\Forms\ProblemTemplateForm\LinearEqTemplateForm;


use App\TeacherModule\Components\Forms\ProblemTemplateForm\ProblemTemplateFormControl;
use App\CoreModule\Helpers\ConstHelper;
use App\CoreModule\Helpers\StringsHelper;
use App\Model\Persistent\Functionality\ProblemTemplate\LinearEquationTemplateFunctionality;
use App\Model\Persistent\Manager\ConstraintEntityManager;
use App\Model\Persistent\Repository\DifficultyRepository;
use App\Model\Persistent\Repository\ProblemConditionRepository;
use App\Model\Persistent\Repository\ProblemConditionTypeRepository;
use App\Model\Persistent\Repository\ProblemTypeRepository;
use App\Model\Persistent\Repository\SubCategoryRepository;
use App\Services\ProblemTemplateSession;
use App\Services\Validator;
use App\TeacherModule\Model\NonPersistent\Entity\LinearEquationTemplateNP;
use App\TeacherModule\Model\NonPersistent\Entity\ProblemTemplateNP;
use App\TeacherModule\Services\PluginContainer;
use Nette\Application\UI\Form;
use Nette\Utils\ArrayHash;

/**
 * Class LinearEqTemplFormControl
 * @package App\TeacherModule\Components\Forms\ProblemTemplateForm\LinearEqTemplateForm
 */
class LinearEqTemplateFormControl extends ProblemTemplateFormControl
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
            'field' => 'subCategory',
            'getter' => 'getSubCategory',
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
    protected $formName = 'LinearEqTemplateForm';

    /**
     * LinearEqTemplateFormControl constructor.
     * @param Validator $validator
     * @param ConstraintEntityManager $entityManager
     * @param DifficultyRepository $difficultyRepository
     * @param ProblemTypeRepository $problemTypeRepository
     * @param SubCategoryRepository $subCategoryRepository
     * @param ProblemConditionTypeRepository $problemConditionTypeRepository
     * @param ProblemConditionRepository $problemConditionRepository
     * @param PluginContainer $pluginContainer
     * @param StringsHelper $stringsHelper
     * @param ConstHelper $constHelper
     * @param ProblemTemplateSession $problemTemplateSession
     * @param LinearEquationTemplateFunctionality $functionality
     */
    public function __construct
    (
        Validator $validator,
        ConstraintEntityManager $entityManager,
        DifficultyRepository $difficultyRepository,
        ProblemTypeRepository $problemTypeRepository,
        SubCategoryRepository $subCategoryRepository,
        ProblemConditionTypeRepository $problemConditionTypeRepository,
        ProblemConditionRepository $problemConditionRepository,
        PluginContainer $pluginContainer,
        StringsHelper $stringsHelper,
        ConstHelper $constHelper,
        ProblemTemplateSession $problemTemplateSession,
        LinearEquationTemplateFunctionality $functionality
    )
    {
        parent::__construct
        (
            $validator, $entityManager,
            $difficultyRepository, $problemTypeRepository, $subCategoryRepository,
            $problemConditionTypeRepository, $problemConditionRepository,
            $pluginContainer,
            $stringsHelper, $constHelper,
            $problemTemplateSession
        );
        $this->functionality = $functionality;
        $this->attachEntities($this->constHelper::LINEAR_EQ);
    }

    /**
     * @return Form
     * @throws \Exception
     */
    public function createComponentForm(): Form
    {
        $form = parent::createComponentForm();

        $form->addSelect('variable', 'Neznámá *', [
            'x' => 'x',
            'y' => 'y',
            'z' => 'z'
        ])
            ->setHtmlAttribute('class', 'form-control')
            ->setHtmlAttribute('placeholder', 'Neznámá šablony.')
            ->setHtmlId('variable');

        return $form;
    }

    /**
     * @param ArrayHash $values
     * @return ProblemTemplateNP
     */
    protected function createNonPersistentEntity(ArrayHash $values): ProblemTemplateNP
    {
        return new LinearEquationTemplateNP($values, $this->entity);
    }

    /**
     * @param ProblemTemplateNP $problemTemplate
     * @return ProblemTemplateNP|null
     */
    public function standardize(ProblemTemplateNP $problemTemplate): ?ProblemTemplateNP
    {
        try{
            $standardized = $this->pluginContainer->getPlugin($this->problemType->getKeyLabel())->standardize($problemTemplate);
        } catch (\Exception $e){
            $this['form']['body']->addError($e->getMessage());
            return null;
        }
        return $standardized;
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