<?php
/**
 * Created by PhpStorm.
 * User: wiedzmin
 * Date: 28.4.19
 * Time: 19:31
 */

namespace App\AdminModule\Presenters;

use App\Components\DataGrids\TemplateGridFactory;
use App\Components\Forms\ProblemTemplateForm\QuadraticEqTemplateForm\QuadraticEqTemplateFormFactory;
use App\Components\HeaderBar\HeaderBarFactory;
use App\Components\SectionHelpModal\ISectionHelpModalFactory;
use App\Components\SideBar\SideBarFactory;
use App\Helpers\ConstHelper;
use App\Helpers\FlashesTranslator;
use App\Model\Persistent\Entity\ProblemTemplate;
use App\Model\Persistent\Functionality\QuadraticEqTemplFunctionality;
use App\Model\Persistent\Repository\QuadraticEqTemplRepository;
use App\Services\Authorizator;
use App\Services\NewtonApiClient;
use App\Services\ProblemTemplateSession;
use Nette\ComponentModel\IComponent;

/**
 * Class QuadraticEqTemplatePresenter
 * @package App\AdminModule\Presenters
 */
class QuadraticEqTemplatePresenter extends ProblemTemplatePresenter
{
    /**
     * QuadraticEqTemplatePresenter constructor.
     * @param Authorizator $authorizator
     * @param NewtonApiClient $newtonApiClient
     * @param HeaderBarFactory $headerBarFactory
     * @param SideBarFactory $sideBarFactory
     * @param FlashesTranslator $flashesTranslator
     * @param QuadraticEqTemplRepository $repository
     * @param QuadraticEqTemplFunctionality $functionality
     * @param TemplateGridFactory $templateGridFactory
     * @param QuadraticEqTemplateFormFactory $problemTemplateFormFactory
     * @param ConstHelper $constHelper
     * @param ISectionHelpModalFactory $sectionHelpModalFactory
     * @param ProblemTemplateSession $problemTemplateSession
     */
    public function __construct
    (
        Authorizator $authorizator, NewtonApiClient $newtonApiClient,
        HeaderBarFactory $headerBarFactory, SideBarFactory $sideBarFactory, FlashesTranslator $flashesTranslator,
        QuadraticEqTemplRepository $repository, QuadraticEqTemplFunctionality $functionality,
        TemplateGridFactory $templateGridFactory, QuadraticEqTemplateFormFactory $problemTemplateFormFactory,
        ConstHelper $constHelper, ISectionHelpModalFactory $sectionHelpModalFactory,
        ProblemTemplateSession $problemTemplateSession
    )
    {
        parent::__construct
        (
            $authorizator, $newtonApiClient,
            $headerBarFactory, $sideBarFactory, $flashesTranslator,
            $templateGridFactory,
            $constHelper, $sectionHelpModalFactory,
            $problemTemplateSession
        );
        $this->repository = $repository;
        $this->functionality = $functionality;
        $this->problemTemplateFormFactory = $problemTemplateFormFactory;
        $this->typeId = $this->constHelper::QUADRATIC_EQ;
    }

    /**
     * @param IComponent $form
     * @param ProblemTemplate $record
     */
    public function setDefaults(IComponent $form, ProblemTemplate $record): void
    {
        parent::setDefaults($form, $record);
        $form['variable']->setDefaultValue($record->getVariable());
    }
}