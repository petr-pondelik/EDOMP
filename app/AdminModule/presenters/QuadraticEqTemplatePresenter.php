<?php
/**
 * Created by PhpStorm.
 * User: wiedzmin
 * Date: 28.4.19
 * Time: 19:31
 */

namespace App\AdminModule\Presenters;

use App\Components\DataGrids\TemplateGridFactory;
use App\Components\Forms\TemplateFormFactory;
use App\Helpers\ConstHelper;
use App\Model\Entity\ProblemTemplate;
use App\Model\Functionality\QuadraticEqTemplFunctionality;
use App\Model\Repository\ProblemTypeRepository;
use App\Model\Repository\QuadraticEqTemplRepository;
use App\Service\Authorizator;
use App\Service\MathService;
use App\Service\ValidationService;
use Nette\ComponentModel\IComponent;

/**
 * Class QuadraticEqTemplatePresenter
 * @package App\AdminModule\Presenters
 */
class QuadraticEqTemplatePresenter extends ProblemTemplatePresenter
{
    /**
     * @var string
     */
    protected $type = "QuadraticEqTemplate";

    /**
     * QuadraticEqTemplatePresenter constructor.
     * @param Authorizator $authorizator
     * @param QuadraticEqTemplRepository $repository
     * @param QuadraticEqTemplFunctionality $functionality
     * @param ProblemTypeRepository $problemTypeRepository
     * @param TemplateGridFactory $templateGridFactory
     * @param TemplateFormFactory $templateFormFactory
     * @param ValidationService $validationService
     * @param MathService $mathService
     * @param ConstHelper $constHelper
     */
    public function __construct
    (
        Authorizator $authorizator,
        QuadraticEqTemplRepository $repository, QuadraticEqTemplFunctionality $functionality,
        ProblemTypeRepository $problemTypeRepository,
        TemplateGridFactory $templateGridFactory, TemplateFormFactory $templateFormFactory,
        ValidationService $validationService, MathService $mathService,
        ConstHelper $constHelper
    )
    {
        parent::__construct
        (
            $authorizator,
            $problemTypeRepository,
            $templateGridFactory, $templateFormFactory,
            $validationService, $mathService,
            $constHelper
        );
        $this->repository = $repository;
        $this->functionality = $functionality;
        $this->typeId = $this->constHelper::QUADRATIC_EQ;
    }

    /**
     * @param IComponent $form
     * @param ProblemTemplate $record
     */
    public function setDefaults(IComponent $form, ProblemTemplate $record)
    {
        parent::setDefaults($form, $record);
        $form["variable"]->setDefaultValue($record->getVariable());
    }
}