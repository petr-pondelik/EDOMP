<?php
/**
 * Created by PhpStorm.
 * User: wiedzmin
 * Date: 27.4.19
 * Time: 22:15
 */

namespace App\TeacherModule\Presenters;

use App\TeacherModule\Components\DataGrids\TemplateGridFactory;
use App\CoreModule\Components\HeaderBar\IHeaderBarFactory;
use App\CoreModule\Components\HelpModal\IHelpModalFactory;
use App\CoreModule\Components\SideBar\ISideBarFactory;
use App\CoreModule\Helpers\ConstHelper;
use App\CoreModule\Helpers\FlashesTranslator;
use App\CoreModule\Model\Persistent\Functionality\ProblemTemplate\LinearEquationTemplateFunctionality;
use App\CoreModule\Model\Persistent\Repository\ProblemTemplate\LinearEquationTemplateRepository;
use App\CoreModule\Services\Authorizator;
use App\TeacherModule\Components\Forms\ProblemTemplateForm\LinearEqTemplateForm\ILinearEqTemplateFormFactory;
use App\TeacherModule\Services\NewtonApiClient;
use App\TeacherModule\Services\ProblemTemplateSession;
use App\CoreModule\Services\Validator;

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