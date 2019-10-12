<?php
/**
 * Created by PhpStorm.
 * User: wiedzmin
 * Date: 29.4.19
 * Time: 18:26
 */

namespace App\AdminModule\Presenters;

use App\Components\DataGrids\TemplateGridFactory;
use App\Components\HeaderBar\IHeaderBarFactory;
use App\Components\SectionHelpModal\ISectionHelpModalFactory;
use App\Components\SideBar\ISideBarFactory;
use App\Helpers\ConstHelper;
use App\Helpers\FlashesTranslator;
use App\Model\Persistent\Functionality\ProblemTemplate\GeometricSequenceTemplateFunctionality;
use App\Model\Persistent\Repository\ProblemTemplate\GeometricSequenceTemplateRepository;
use App\Services\Authorizator;
use App\Services\NewtonApiClient;
use App\Services\ProblemTemplateSession;
use App\Services\Validator;
use ProblemTemplateForm\GeometricSeqTemplateForm\IGeometricSeqTemplateFormFactory;

/**
 * Class GeometricSeqTemplatePresenter
 * @package App\AdminModule\Presenters
 */
class GeometricSeqTemplatePresenter extends ProblemTemplatePresenter
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
     * @param ISectionHelpModalFactory $sectionHelpModalFactory
     * @param ProblemTemplateSession $problemTemplateSession
     */
    public function __construct
    (
        Authorizator $authorizator, Validator $validator, NewtonApiClient $newtonApiClient,
        IHeaderBarFactory $headerBarFactory, ISideBarFactory $sideBarFactory, FlashesTranslator $flashesTranslator,
        GeometricSequenceTemplateRepository $repository, GeometricSequenceTemplateFunctionality $functionality,
        TemplateGridFactory $templateGridFactory, IGeometricSeqTemplateFormFactory $formFactory,
        ConstHelper $constHelper,
        ISectionHelpModalFactory $sectionHelpModalFactory,
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