<?php
/**
 * Created by PhpStorm.
 * User: wiedzmin
 * Date: 28.4.19
 * Time: 21:34
 */

namespace App\AdminModule\Presenters;

use App\Components\DataGrids\TemplateGridFactory;
use App\Components\Forms\TemplateFormFactory;
use App\Helpers\ConstHelper;
use App\Model\Entity\ProblemTemplate;
use App\Model\Functionality\ArithmeticSeqTemplFunctionality;
use App\Model\Repository\ArithmeticSeqTemplRepository;
use App\Model\Repository\ProblemTypeRepository;
use App\Service\MathService;
use App\Service\ValidationService;
use Nette\ComponentModel\IComponent;

/**
 * Class ArithmeticSeqTemplatePresenter
 * @package App\AdminModule\Presenters
 */
class ArithmeticSeqTemplatePresenter extends ProblemTemplatePresenter
{
    /**
     * @var string
     */
    protected $type = "ArithmeticSeqTemplate";

    public function __construct
    (
        ArithmeticSeqTemplRepository $repository, ArithmeticSeqTemplFunctionality $functionality,
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
        $this->typeId = $this->constHelper::ARITHMETIC_SEQ;
    }

    /**
     * @param IComponent $form
     * @param ProblemTemplate $record
     */
    public function setDefaults(IComponent $form, ProblemTemplate $record)
    {
        parent::setDefaults($form, $record); // TODO: Change the autogenerated stub
        $form["variable"]->setDefaultValue($record->getVariable());
        $form["first_n"]->setDefaultValue($record->getFirstN());
    }
}