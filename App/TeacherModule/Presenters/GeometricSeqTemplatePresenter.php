<?php
/**
 * Created by PhpStorm.
 * User: wiedzmin
 * Date: 29.4.19
 * Time: 18:26
 */

namespace App\TeacherModule\Presenters;

use App\TeacherModule\Components\DataGrids\TemplateGridFactory;
use App\CoreModule\Components\HeaderBar\IHeaderBarFactory;
use App\CoreModule\Components\HelpModal\IHelpModalFactory;
use App\CoreModule\Components\SideBar\ISideBarFactory;
use App\CoreModule\Helpers\ConstHelper;
use App\CoreModule\Helpers\FlashesTranslator;
use App\CoreModule\Model\Persistent\Functionality\ProblemTemplate\GeometricSequenceTemplateFunctionality;
use App\CoreModule\Model\Persistent\Repository\ProblemTemplate\GeometricSequenceTemplateRepository;
use App\CoreModule\Services\Authorizator;
use App\TeacherModule\Components\Forms\ProblemTemplateForm\GeometricSeqTemplateForm\IGeometricSeqTemplateFormFactory;
use App\TeacherModule\Services\NewtonApiClient;
use App\TeacherModule\Services\ProblemTemplateSession;
use App\CoreModule\Services\Validator;

/**
 * Class GeometricSeqTemplatePresenter
 * @package App\TeacherModule\Presenters
 */
final class GeometricSeqTemplatePresenter extends ProblemTemplatePresenter
{
    /**
     * GeometricSeqTemplatePresenter constructor.
     * @param Authorizator $authorizator
     * @param Validator $validator
     * @param NewtonApiClient $newtonApiClient
     * @param IHeaderBarFactory $headerBarFactory
     * @param ISideBarFactory $sideBarFactory
     * @param FlashesTranslator $flashesTranslator
     * @param GeometricSequenceTemplateRepository $repository
     * @param GeometricSequenceTemplateFunctionality $functionality
     * @param TemplateGridFactory $templateGridFactory
     * @param IGeometricSeqTemplateFormFactory $formFactory
     * @param ConstHelper $constHelper
     * @param IHelpModalFactory $sectionHelpModalFactory
     * @param ProblemTemplateSession $problemTemplateSession
     */
    public function __construct
    (
        Authorizator $authorizator, Validator $validator, NewtonApiClient $newtonApiClient,
        IHeaderBarFactory $headerBarFactory, ISideBarFactory $sideBarFactory, FlashesTranslator $flashesTranslator,
        GeometricSequenceTemplateRepository $repository, GeometricSequenceTemplateFunctionality $functionality,
        TemplateGridFactory $templateGridFactory, IGeometricSeqTemplateFormFactory $formFactory,
        ConstHelper $constHelper,
        IHelpModalFactory $sectionHelpModalFactory,
        ProblemTemplateSession $problemTemplateSession
    )
    {
        parent::__construct
        (
            $authorizator, $validator, $newtonApiClient,
            $headerBarFactory, $sideBarFactory, $flashesTranslator,
            $templateGridFactory,
            $constHelper, $sectionHelpModalFactory,
            $problemTemplateSession,
            $repository, $functionality, $formFactory
        );
        $this->typeId = $this->constHelper::GEOMETRIC_SEQ;
    }
}