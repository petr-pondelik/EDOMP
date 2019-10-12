<?php
/**
 * Created by PhpStorm.
 * User: wiedzmin
 * Date: 28.4.19
 * Time: 19:31
 */

namespace App\AdminModule\Presenters;

use App\Components\DataGrids\TemplateGridFactory;
use App\Components\Forms\ProblemTemplateForm\QuadraticEqTemplateForm\IQuadraticEqTemplateFormFactory;
use App\Components\HeaderBar\IHeaderBarFactory;
use App\Components\SectionHelpModal\ISectionHelpModalFactory;
use App\Components\SideBar\ISideBarFactory;
use App\Helpers\ConstHelper;
use App\Helpers\FlashesTranslator;
use App\Model\Persistent\Functionality\ProblemTemplate\QuadraticEquationTemplateFunctionality;
use App\Model\Persistent\Repository\ProblemTemplate\QuadraticEquationTemplateRepository;
use App\Services\Authorizator;
use App\Services\NewtonApiClient;
use App\Services\ProblemTemplateSession;
use App\Services\Validator;

/**
 * Class QuadraticEqTemplatePresenter
 * @package App\AdminModule\Presenters
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
     * @param ISectionHelpModalFactory $sectionHelpModalFactory
     * @param ProblemTemplateSession $problemTemplateSession
     */
    public function __construct
    (
        Authorizator $authorizator, Validator $validator, NewtonApiClient $newtonApiClient,
        IHeaderBarFactory $headerBarFactory, ISideBarFactory $sideBarFactory, FlashesTranslator $flashesTranslator,
        QuadraticEquationTemplateRepository $repository, QuadraticEquationTemplateFunctionality $functionality,
        TemplateGridFactory $templateGridFactory, IQuadraticEqTemplateFormFactory $problemTemplateFormFactory,
        ConstHelper $constHelper, ISectionHelpModalFactory $sectionHelpModalFactory,
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