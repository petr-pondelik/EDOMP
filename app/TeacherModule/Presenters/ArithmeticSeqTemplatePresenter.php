<?php
/**
 * Created by PhpStorm.
 * User: wiedzmin
 * Date: 28.4.19
 * Time: 21:34
 */

namespace App\TeacherModule\Presenters;

use App\Components\DataGrids\TemplateGridFactory;
use App\CoreModule\Components\HeaderBar\IHeaderBarFactory;
use App\CoreModule\Components\HelpModal\IHelpModalFactory;
use App\CoreModule\Components\SideBar\ISideBarFactory;
use App\CoreModule\Helpers\ConstHelper;
use App\CoreModule\Helpers\FlashesTranslator;
use App\Model\Persistent\Functionality\ProblemTemplate\ArithmeticSequenceTemplateFunctionality;
use App\Model\Persistent\Repository\ProblemTemplate\ArithmeticSequenceTemplateRepository;
use App\Services\Authorizator;
use App\TeacherModule\Components\Forms\ProblemTemplateForm\ArithmeticSeqTemplateForm\IArithmeticSeqTemplateFormFactory;
use App\TeacherModule\Services\NewtonApiClient;
use App\Services\ProblemTemplateSession;
use App\Services\Validator;

/**
 * Class ArithmeticSeqTemplatePresenter
 * @package App\TeacherModule\Presenters
 */
class ArithmeticSeqTemplatePresenter extends ProblemTemplatePresenter
{
    /**
     * ArithmeticSeqTemplatePresenter constructor.
     * @param Authorizator $authorizator
     * @param Validator $validator
     * @param NewtonApiClient $newtonApiClient
     * @param IHeaderBarFactory $headerBarFactory
     * @param ISideBarFactory $sideBarFactory
     * @param FlashesTranslator $flashesTranslator
     * @param ArithmeticSequenceTemplateRepository $repository
     * @param ArithmeticSequenceTemplateFunctionality $functionality
     * @param TemplateGridFactory $templateGridFactory
     * @param IArithmeticSeqTemplateFormFactory $formFactory
     * @param ConstHelper $constHelper
     * @param IHelpModalFactory $sectionHelpModalFactory
     * @param ProblemTemplateSession $problemTemplateSession
     */
    public function __construct
    (
        Authorizator $authorizator, Validator $validator, NewtonApiClient $newtonApiClient,
        IHeaderBarFactory $headerBarFactory, ISideBarFactory $sideBarFactory, FlashesTranslator $flashesTranslator,
        ArithmeticSequenceTemplateRepository $repository, ArithmeticSequenceTemplateFunctionality $functionality,
        TemplateGridFactory $templateGridFactory, IArithmeticSeqTemplateFormFactory $formFactory,
        ConstHelper $constHelper,
        IHelpModalFactory $sectionHelpModalFactory,
        ProblemTemplateSession $problemTemplateSession
    )
    {
        parent::__construct
        (
            $authorizator, $validator, $newtonApiClient,
            $headerBarFactory, $sideBarFactory, $flashesTranslator,
            $templateGridFactory, $constHelper, $sectionHelpModalFactory,
            $problemTemplateSession,
            $repository, $functionality, $formFactory
        );
        $this->typeId = $this->constHelper::ARITHMETIC_SEQ;
    }
}