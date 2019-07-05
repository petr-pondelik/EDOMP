<?php
/**
 * Created by PhpStorm.
 * User: wiedzmin
 * Date: 28.4.19
 * Time: 21:34
 */

namespace App\AdminModule\Presenters;

use App\Components\DataGrids\TemplateGridFactory;
use App\Components\Forms\ProblemTemplateForm\ArithmeticSeqTemplateForm\ArithmeticSeqTemplateFormFactory;
use App\Components\Forms\ProblemTemplateForm\ProblemTemplateFormFactory;
use App\Components\HeaderBar\HeaderBarFactory;
use App\Components\ProblemTemplateHelp\ProblemTemplateHelpControl;
use App\Components\ProblemTemplateHelp\ProblemTemplateHelpFactory;
use App\Components\SideBar\SideBarFactory;
use App\Helpers\ConstHelper;
use App\Helpers\FlashesTranslator;
use App\Model\Entity\ProblemTemplate;
use App\Model\Functionality\ArithmeticSeqTemplFunctionality;
use App\Model\Repository\ArithmeticSeqTemplRepository;
use App\Services\Authorizator;
use App\Services\NewtonApiClient;
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
    protected $type = 'ArithmeticSeqTemplate';

    /**
     * ArithmeticSeqTemplatePresenter constructor.
     * @param Authorizator $authorizator
     * @param NewtonApiClient $newtonApiClient
     * @param HeaderBarFactory $headerBarFactory
     * @param SideBarFactory $sideBarFactory
     * @param FlashesTranslator $flashesTranslator
     * @param ArithmeticSeqTemplRepository $repository
     * @param ArithmeticSeqTemplFunctionality $functionality
     * @param TemplateGridFactory $templateGridFactory
     * @param ArithmeticSeqTemplateFormFactory $problemTemplateFormFactory
     * @param ConstHelper $constHelper
     * @param ProblemTemplateHelpFactory $problemTemplateHelpFactory
     */
    public function __construct
    (
        Authorizator $authorizator, NewtonApiClient $newtonApiClient,
        HeaderBarFactory $headerBarFactory, SideBarFactory $sideBarFactory, FlashesTranslator $flashesTranslator,
        ArithmeticSeqTemplRepository $repository, ArithmeticSeqTemplFunctionality $functionality,
        TemplateGridFactory $templateGridFactory, ArithmeticSeqTemplateFormFactory $problemTemplateFormFactory,
        ConstHelper $constHelper, ProblemTemplateHelpFactory $problemTemplateHelpFactory
    )
    {
        parent::__construct
        (
            $authorizator, $newtonApiClient,
            $headerBarFactory, $sideBarFactory, $flashesTranslator,
            $templateGridFactory,
            $constHelper, $problemTemplateHelpFactory
        );
        $this->repository = $repository;
        $this->functionality = $functionality;
        $this->problemTemplateFormFactory = $problemTemplateFormFactory;
        $this->typeId = $this->constHelper::ARITHMETIC_SEQ;
    }

    /**
     * @param IComponent $form
     * @param ProblemTemplate $record
     */
    public function setDefaults(IComponent $form, ProblemTemplate $record): void
    {
        parent::setDefaults($form, $record);
        $form['variable']->setDefaultValue($record->getVariable());
        $form['first_n']->setDefaultValue($record->getFirstN());
    }
}