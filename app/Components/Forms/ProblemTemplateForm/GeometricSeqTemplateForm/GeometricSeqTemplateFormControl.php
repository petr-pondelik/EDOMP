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
use App\Model\NonPersistent\Entity\GeometricSequenceTemplateNP;
use App\Model\NonPersistent\Entity\ProblemTemplateNP;
use App\Model\Persistent\Functionality\BaseFunctionality;
use App\Model\Persistent\Repository\DifficultyRepository;
use App\Model\Persistent\Repository\ProblemConditionRepository;
use App\Model\Persistent\Repository\ProblemConditionTypeRepository;
use App\Model\Persistent\Repository\ProblemTypeRepository;
use App\Model\Persistent\Repository\SubCategoryRepository;
use App\Services\PluginContainer;
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
    protected $baseItems = [
        [
            'field' => 'variable',
            'validation' => 'variable'
        ],
        [
            'field' => 'subCategory',
            'validation' => 'notEmpty'
        ],
        [
            'field' => 'difficulty',
            'validation' => 'notEmpty'
        ],
        [
            'field' => 'firstN',
            'validation' => 'notEmptyPositive'
        ]
    ];

    /**
     * @var string
     */
    protected $formName = 'GeometricSeqTemplateForm';

    /**
     * GeometricSeqTemplateFormControl constructor.
     * @param Validator $validator
     * @param BaseFunctionality $functionality
     * @param DifficultyRepository $difficultyRepository
     * @param ProblemTypeRepository $problemTypeRepository
     * @param SubCategoryRepository $subCategoryRepository
     * @param ProblemConditionTypeRepository $problemConditionTypeRepository
     * @param ProblemConditionRepository $problemConditionRepository
     * @param PluginContainer $pluginContainer
     * @param ConstHelper $constHelper
     * @param bool $edit
     */
    public function __construct
    (
        Validator $validator, BaseFunctionality $functionality, DifficultyRepository $difficultyRepository,
        ProblemTypeRepository $problemTypeRepository, SubCategoryRepository $subCategoryRepository,
        ProblemConditionTypeRepository $problemConditionTypeRepository, ProblemConditionRepository $problemConditionRepository,
        PluginContainer $pluginContainer, ConstHelper $constHelper, bool $edit = false)
    {
        parent::__construct
        (
            $validator, $functionality, $difficultyRepository, $problemTypeRepository, $subCategoryRepository,
            $problemConditionTypeRepository, $problemConditionRepository, $pluginContainer, $constHelper, $edit
        );
        $this->attachEntities($this->constHelper::GEOMETRIC_SEQ);
    }

    /**
     * @return Form
     * @throws \Exception
     */
    public function createComponentForm(): Form
    {
        $form = parent::createComponentForm();

        $form->addText('variable', 'Index *')
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
     * @return GeometricSequenceTemplateNP
     */
    protected function createNonPersistentEntity(ArrayHash $values): GeometricSequenceTemplateNP
    {
        return new GeometricSequenceTemplateNP($values);
    }

    /**
     * @param ProblemTemplateNP $problemTemplate
     * @return ProblemTemplateNP|null
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function standardize(ProblemTemplateNP $problemTemplate): ?ProblemTemplateNP
    {
        try{
            $problemTemplate = $this->pluginContainer->standardizeGeometricSequence($problemTemplate);
        } catch (EquationException $e){
            $this['form']['body']->addError('Zadaný výraz není validním předpisem posloupnosti.');
            return null;
        } catch (\Exception $e){
            $this['form']['body']->addError($e->getMessage());
        }
        return $problemTemplate;
    }

}