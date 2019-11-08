<?php
/**
 * Created by PhpStorm.
 * User: wiedzmin
 * Date: 27.4.19
 * Time: 22:15
 */

namespace App\TeacherModule\Presenters;

use App\Components\DataGrids\TemplateGridFactory;
use App\CoreModule\Components\HeaderBar\IHeaderBarFactory;
use App\CoreModule\Components\HelpModal\IHelpModalFactory;
use App\CoreModule\Components\SideBar\ISideBarFactory;
use App\CoreModule\Helpers\ConstHelper;
use App\CoreModule\Helpers\FlashesTranslator;
use App\Model\Persistent\Functionality\ProblemTemplate\LinearEquationTemplateFunctionality;
use App\Model\Persistent\Repository\ProblemTemplate\LinearEquationTemplateRepository;
use App\Services\Authorizator;
use App\TeacherModule\Components\Forms\ProblemTemplateForm\LinearEqTemplateForm\ILinearEqTemplateFormFactory;
use App\TeacherModule\Services\NewtonApiClient;
use App\Services\ProblemTemplateSession;
use App\Services\Validator;

/**
 * Class LinearEqTemplatePresenter
 * @package App\TeacherModule\Presenters
 */
class LinearEqTemplatePresenter extends ProblemTemplatePresenter
{
    /**
     * LinearEqTemplatePresenter constructor.
     * @param Authorizator $authorizator
     * @param Validator $validator
     * @param NewtonApiClient $newtonApiClient
     * @param IHeaderBarFactory $headerBarFactory
     * @param ISideBarFactory $sideBarFactory
     * @param FlashesTranslator $flashesTranslator
     * @param LinearEquationTemplateRepository $repository
     * @param LinearEquationTemplateFunctionality $functionality
     * @param TemplateGridFactory $templateGridFactory
     * @param ILinearEqTemplateFormFactory $problemTemplateFormFactory
     * @param ConstHelper $constHelper
     * @param IHelpModalFactory $sectionHelpModalFactory
     * @param ProblemTemplateSession $problemTemplateSession
     */
    public function __construct
    (
        Authorizator $authorizator, Validator $validator, NewtonApiClient $newtonApiClient,
        IHeaderBarFactory $headerBarFactory, ISideBarFactory $sideBarFactory, FlashesTranslator $flashesTranslator,
        LinearEquationTemplateRepository $repository, LinearEquationTemplateFunctionality $functionality,
        TemplateGridFactory $templateGridFactory, ILinearEqTemplateFormFactory $problemTemplateFormFactory,
        ConstHelper $constHelper, IHelpModalFactory $sectionHelpModalFactory,
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
            $repository, $functionality, $problemTemplateFormFactory
        );
        $this->typeId = $this->constHelper::LINEAR_EQ;
    }
}