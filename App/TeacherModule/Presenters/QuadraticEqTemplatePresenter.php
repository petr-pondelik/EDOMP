<?php
/**
 * Created by PhpStorm.
 * User: wiedzmin
 * Date: 28.4.19
 * Time: 19:31
 */

namespace App\TeacherModule\Presenters;

use App\TeacherModule\Components\DataGrids\TemplateGridFactory;
use App\CoreModule\Components\HeaderBar\IHeaderBarFactory;
use App\CoreModule\Components\HelpModal\IHelpModalFactory;
use App\CoreModule\Components\SideBar\ISideBarFactory;
use App\CoreModule\Helpers\ConstHelper;
use App\CoreModule\Helpers\FlashesTranslator;
use App\CoreModule\Model\Persistent\Functionality\ProblemTemplate\QuadraticEquationTemplateFunctionality;
use App\CoreModule\Model\Persistent\Repository\ProblemTemplate\QuadraticEquationTemplateRepository;
use App\CoreModule\Services\Authorizator;
use App\TeacherModule\Components\Forms\ProblemTemplateForm\QuadraticEqTemplateForm\IQuadraticEqTemplateFormFactory;
use App\TeacherModule\Services\NewtonApiClient;
use App\TeacherModule\Services\ProblemTemplateSession;
use App\CoreModule\Services\Validator;

/**
 * Class QuadraticEqTemplatePresenter
 * @package App\TeacherModule\Presenters
 */
class QuadraticEqTemplatePresenter extends ProblemTemplatePresenter
{
    /**
     * QuadraticEqTemplatePresenter constructor.
     * @param Authorizator $authorizator
     * @param Validator $validator
     * @param NewtonApiClient $newtonApiClient
     * @param IHeaderBarFactory $headerBarFactory
     * @param ISideBarFactory $sideBarFactory
     * @param FlashesTranslator $flashesTranslator
     * @param QuadraticEquationTemplateRepository $repository
     * @param QuadraticEquationTemplateFunctionality $functionality
     * @param TemplateGridFactory $templateGridFactory
     * @param IQuadraticEqTemplateFormFactory $problemTemplateFormFactory
     * @param ConstHelper $constHelper
     * @param IHelpModalFactory $sectionHelpModalFactory
     * @param ProblemTemplateSession $problemTemplateSession
     */
    public function __construct
    (
        Authorizator $authorizator, Validator $validator, NewtonApiClient $newtonApiClient,
        IHeaderBarFactory $headerBarFactory, ISideBarFactory $sideBarFactory, FlashesTranslator $flashesTranslator,
        QuadraticEquationTemplateRepository $repository, QuadraticEquationTemplateFunctionality $functionality,
        TemplateGridFactory $templateGridFactory, IQuadraticEqTemplateFormFactory $problemTemplateFormFactory,
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
        $this->typeId = $this->constHelper::QUADRATIC_EQ;
    }
}