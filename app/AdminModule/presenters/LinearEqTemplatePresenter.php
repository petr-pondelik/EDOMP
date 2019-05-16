<?php
/**
 * Created by PhpStorm.
 * User: wiedzmin
 * Date: 27.4.19
 * Time: 22:15
 */

namespace App\AdminModule\Presenters;

use App\Components\DataGrids\TemplateGridFactory;
use App\Components\Forms\TemplateFormFactory;
use App\Helpers\ConstHelper;
use App\Model\Entity\LinearEqTempl;
use App\Model\Entity\ProblemTemplate;
use App\Model\Functionality\LinearEqTemplFunctionality;
use App\Model\Repository\LinearEqTemplRepository;
use App\Model\Repository\ProblemTypeRepository;
use App\Service\MathService;
use App\Service\ValidationService;
use Nette\ComponentModel\IComponent;

/**
 * Class LinearEqTemplatePresenter
 * @package App\AdminModule\Presenters
 */
class LinearEqTemplatePresenter extends ProblemTemplatePresenter
{
    /**
     * @var string
     */
    protected $type = "LinearEqTemplate";

    /**
     * LinearEqTemplatePresenter constructor.
     * @param LinearEqTemplRepository $repository
     * @param LinearEqTemplFunctionality $functionality
     * @param ProblemTypeRepository $problemTypeRepository
     * @param TemplateGridFactory $templateGridFactory
     * @param TemplateFormFactory $templateFormFactory
     * @param ValidationService $validationService
     * @param MathService $mathService
     * @param ConstHelper $constHelper
     */
    public function __construct
    (
        LinearEqTemplRepository $repository, LinearEqTemplFunctionality $functionality,
        ProblemTypeRepository $problemTypeRepository,
        TemplateGridFactory $templateGridFactory, TemplateFormFactory $templateFormFactory,
        ValidationService $validationService, MathService $mathService,
        ConstHelper $constHelper
    )
    {
        parent::__construct
        (
            $problemTypeRepository,
            $templateGridFactory, $templateFormFactory,
            $validationService, $mathService,
            $constHelper
        );
        $this->repository = $repository;
        $this->functionality = $functionality;
        $this->typeId = $this->constHelper::LINEAR_EQ;
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