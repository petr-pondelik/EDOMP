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
use App\Components\HeaderBar\HeaderBarFactory;
use App\Components\SectionHelpModal\ISectionHelpModalFactory;
use App\Components\SideBar\SideBarFactory;
use App\Helpers\ConstHelper;
use App\Helpers\FlashesTranslator;
use App\Model\Persistent\Entity\ProblemTemplate\ProblemTemplate;
use App\Model\Persistent\Functionality\ProblemTemplate\ArithmeticSequenceTemplateFunctionality;
use App\Model\Persistent\Repository\ProblemTemplate\ArithmeticSequenceTemplateRepository;
use App\Services\Authorizator;
use App\Services\NewtonApiClient;
use App\Services\ProblemTemplateSession;
use Nette\ComponentModel\IComponent;

/**
 * Class ArithmeticSeqTemplatePresenter
 * @package App\AdminModule\Presenters
 */
class ArithmeticSeqTemplatePresenter extends ProblemTemplatePresenter
{
    /**
     * ArithmeticSeqTemplatePresenter constructor.
     * @param Authorizator $authorizator
     * @param NewtonApiClient $newtonApiClient
     * @param HeaderBarFactory $headerBarFactory
     * @param SideBarFactory $sideBarFactory
     * @param FlashesTranslator $flashesTranslator
     * @param ArithmeticSequenceTemplateRepository $repository
     * @param ArithmeticSequenceTemplateFunctionality $functionality
     * @param TemplateGridFactory $templateGridFactory
     * @param ArithmeticSeqTemplateFormFactory $problemTemplateFormFactory
     * @param ConstHelper $constHelper
     * @param ISectionHelpModalFactory $sectionHelpModalFactory
     * @param ProblemTemplateSession $problemTemplateSession
     */
    public function __construct
    (
        Authorizator $authorizator, NewtonApiClient $newtonApiClient,
        HeaderBarFactory $headerBarFactory, SideBarFactory $sideBarFactory, FlashesTranslator $flashesTranslator,
        ArithmeticSequenceTemplateRepository $repository, ArithmeticSequenceTemplateFunctionality $functionality,
        TemplateGridFactory $templateGridFactory, ArithmeticSeqTemplateFormFactory $problemTemplateFormFactory,
        ConstHelper $constHelper,
        ISectionHelpModalFactory $sectionHelpModalFactory,
        ProblemTemplateSession $problemTemplateSession
    )
    {
        parent::__construct
        (
            $authorizator, $newtonApiClient,
            $headerBarFactory, $sideBarFactory, $flashesTranslator,
            $templateGridFactory, $constHelper, $sectionHelpModalFactory,
            $problemTemplateSession
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
        $form['indexVariable']->setDefaultValue($record->getIndexVariable());
        $form['firstN']->setDefaultValue($record->getFirstN());
    }
}