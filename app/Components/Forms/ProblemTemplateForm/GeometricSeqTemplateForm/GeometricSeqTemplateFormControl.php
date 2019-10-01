<?php
/**
 * Created by PhpStorm.
 * User: wiedzmin
 * Date: 5.7.19
 * Time: 22:56
 */

namespace ProblemTemplateForm\GeometricSeqTemplateForm;


use App\Components\Forms\ProblemTemplateForm\ProblemTemplateFormControl;
use App\Exceptions\EquationException;
use App\Helpers\ConstHelper;
use App\Helpers\StringsHelper;
use App\Model\NonPersistent\Entity\GeometricSequenceTemplateNP;
use App\Model\NonPersistent\Entity\ProblemTemplateNP;
use App\Model\Persistent\Functionality\BaseFunctionality;
use App\Model\Persistent\Functionality\ProblemTemplate\GeometricSequenceTemplateFunctionality;
use App\Model\Persistent\Manager\ConstraintEntityManager;
use App\Model\Persistent\Repository\DifficultyRepository;
use App\Model\Persistent\Repository\ProblemConditionRepository;
use App\Model\Persistent\Repository\ProblemConditionTypeRepository;
use App\Model\Persistent\Repository\ProblemTypeRepository;
use App\Model\Persistent\Repository\SubCategoryRepository;
use App\Plugins\ProblemPlugin;
use App\Services\PluginContainer;
use App\Services\ProblemTemplateSession;
use App\Services\Validator;
use Nette\Application\UI\Form;
use Nette\Utils\ArrayHash;

/**
 * Class GeometricSeqTemplateFormControl
 * @package ProblemTemplateForm\GeometricSeqTemplateForm
 */
class GeometricSeqTemplateFormControl extends ProblemTemplateFormControl
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
            'field' => 'subCategory',
            'getter' => 'getSubCategory',
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
    protected $formName = 'GeometricSeqTemplateForm';

    /**
     * GeometricSeqTemplateFormControl constructor.
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
     * @param GeometricSequenceTemplateFunctionality $functionality
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
        GeometricSequenceTemplateFunctionality $functionality
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
        $this->attachEntities($this->constHelper::GEOMETRIC_SEQ);
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
     * @return ProblemTemplateNP
     */
    protected function createNonPersistentEntity(ArrayHash $values): ProblemTemplateNP
    {
        return new GeometricSequenceTemplateNP($values);
    }

    /**
     * @param ProblemTemplateNP $problemTemplate
     * @return ProblemTemplateNP|null
     */
    public function standardize(ProblemTemplateNP $problemTemplate): ?ProblemTemplateNP
    {
        try{
            $standardized = $this->pluginContainer->getPlugin($this->problemType->getKeyLabel())->standardize($problemTemplate);
        } catch (EquationException $e){
            $this['form']['body']->addError('Zadaný výraz není validním předpisem posloupnosti.');
            return null;
        } catch (\Exception $e){
            $this['form']['body']->addError($e->getMessage());
        }
        return $standardized;
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