<?php
/**
 * Created by PhpStorm.
 * User: wiedzmin
 * Date: 5.7.19
 * Time: 22:46
 */

namespace App\Components\Forms\ProblemTemplateForm\QuadraticEqTemplateForm;

use App\Components\Forms\ProblemTemplateForm\ProblemTemplateFormControl;
use App\Helpers\ConstHelper;
use App\Helpers\StringsHelper;
use App\Model\NonPersistent\Entity\ProblemTemplateNP;
use App\Model\NonPersistent\Entity\QuadraticEquationTemplateNP;
use App\Model\Persistent\Functionality\BaseFunctionality;
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
 * Class QuadraticEqTemplateFormControl
 * @package App\Components\Forms\ProblemTemplateForm\QuadraticEqTemplateForm
 */
class QuadraticEqTemplateFormControl extends ProblemTemplateFormControl
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
    protected $formName = 'QuadraticEqTemplateForm';

    /**
     * QuadraticEqTemplateFormControl constructor.
     * @param Validator $validator
     * @param BaseFunctionality $functionality
     * @param DifficultyRepository $difficultyRepository
     * @param ProblemTypeRepository $problemTypeRepository
     * @param SubCategoryRepository $subCategoryRepository
     * @param ProblemConditionTypeRepository $problemConditionTypeRepository
     * @param ProblemConditionRepository $problemConditionRepository
     * @param ProblemPlugin $problemTemplatePlugin
     * @param PluginContainer $pluginContainer
     * @param StringsHelper $stringsHelper
     * @param ConstHelper $constHelper
     * @param ProblemTemplateSession $problemTemplateSession
     * @param bool $edit
     */
    public function __construct
    (
        Validator $validator, BaseFunctionality $functionality, DifficultyRepository $difficultyRepository,
        ProblemTypeRepository $problemTypeRepository, SubCategoryRepository $subCategoryRepository,
        ProblemConditionTypeRepository $problemConditionTypeRepository, ProblemConditionRepository $problemConditionRepository,
        ProblemPlugin $problemTemplatePlugin,
        PluginContainer $pluginContainer,
        StringsHelper $stringsHelper, ConstHelper $constHelper,
        ProblemTemplateSession $problemTemplateSession,
        bool $edit = false
    )
    {
        parent::__construct
        (
            $validator, $functionality, $difficultyRepository, $problemTypeRepository, $subCategoryRepository,
            $problemConditionTypeRepository, $problemConditionRepository,
            $problemTemplatePlugin,
            $pluginContainer,
            $stringsHelper, $constHelper,
            $problemTemplateSession,
            $edit
        );
        $this->attachEntities($this->constHelper::QUADRATIC_EQ);
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
        return new QuadraticEquationTemplateNP($values);
    }

    /**
     * @param ProblemTemplateNP $problemTemplate
     * @return ProblemTemplateNP|null
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function standardize(ProblemTemplateNP $problemTemplate): ?ProblemTemplateNP
    {
        try{
            $standardized = $this->pluginContainer->standardizeQuadraticEquation($problemTemplate);
        } catch (\Exception $e){
            $this['form']['body']->addError($e->getMessage());
            return null;
        }
        return $standardized;
    }
}