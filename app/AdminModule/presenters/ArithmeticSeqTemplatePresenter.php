<?php
/**
 * Created by PhpStorm.
 * User: wiedzmin
 * Date: 28.4.19
 * Time: 21:34
 */

namespace App\AdminModule\Presenters;

use App\Components\DataGrids\TemplateGridFactory;
use App\Components\Forms\ProblemTemplateForm\ProblemTemplateFormFactory;
use App\Components\HeaderBar\HeaderBarFactory;
use App\Components\SideBar\SideBarFactory;
use App\Helpers\ConstHelper;
use App\Helpers\FlashesTranslator;
use App\Model\Entity\ProblemTemplate;
use App\Model\Functionality\ArithmeticSeqTemplFunctionality;
use App\Model\Repository\ArithmeticSeqTemplRepository;
use App\Service\Authorizator;
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

    /**
     * ArithmeticSeqTemplatePresenter constructor.
     * @param Authorizator $authorizator
     * @param HeaderBarFactory $headerBarFactory
     * @param SideBarFactory $sideBarFactory
     * @param FlashesTranslator $flashesTranslator
     * @param ArithmeticSeqTemplRepository $repository
     * @param ArithmeticSeqTemplFunctionality $functionality
     * @param TemplateGridFactory $templateGridFactory
     * @param ProblemTemplateFormFactory $problemTemplateFormFactory
     * @param ConstHelper $constHelper
     */
    public function __construct
    (
        Authorizator $authorizator,
        HeaderBarFactory $headerBarFactory, SideBarFactory $sideBarFactory, FlashesTranslator $flashesTranslator,
        ArithmeticSeqTemplRepository $repository, ArithmeticSeqTemplFunctionality $functionality,
        TemplateGridFactory $templateGridFactory, ProblemTemplateFormFactory $problemTemplateFormFactory,
        ConstHelper $constHelper
    )
    {
        parent::__construct
        (
            $authorizator,
            $headerBarFactory, $sideBarFactory, $flashesTranslator,
            $templateGridFactory, $problemTemplateFormFactory,
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
    public function setDefaults(IComponent $form, ProblemTemplate $record): void
    {
        parent::setDefaults($form, $record);
        $form["variable"]->setDefaultValue($record->getVariable());
        $form["first_n"]->setDefaultValue($record->getFirstN());
    }
}