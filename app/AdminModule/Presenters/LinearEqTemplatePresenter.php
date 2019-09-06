<?php
/**
 * Created by PhpStorm.
 * User: wiedzmin
 * Date: 27.4.19
 * Time: 22:15
 */

namespace App\AdminModule\Presenters;

use App\Components\DataGrids\TemplateGridFactory;
use App\Components\Forms\ProblemTemplateForm\LinearEqTemplateForm\LinearEqTemplateFormFactory;
use App\Components\HeaderBar\HeaderBarFactory;
use App\Components\SectionHelpModal\ISectionHelpModalFactory;
use App\Components\SideBar\SideBarFactory;
use App\Helpers\ConstHelper;
use App\Helpers\FlashesTranslator;
use App\Model\Persistent\Entity\ProblemTemplate;
use App\Model\Persistent\Functionality\LinearEqTemplFunctionality;
use App\Model\Persistent\Repository\LinearEqTemplRepository;
use App\Services\Authorizator;
use App\Services\NewtonApiClient;
use App\Services\ProblemTemplateStatus;
use Nette\ComponentModel\IComponent;

/**
 * Class LinearEqTemplatePresenter
 * @package App\AdminModule\Presenters
 */
class LinearEqTemplatePresenter extends ProblemTemplatePresenter
{
    /**
     * LinearEqTemplatePresenter constructor.
     * @param Authorizator $authorizator
     * @param NewtonApiClient $newtonApiClient
     * @param HeaderBarFactory $headerBarFactory
     * @param SideBarFactory $sideBarFactory
     * @param FlashesTranslator $flashesTranslator
     * @param LinearEqTemplRepository $repository
     * @param LinearEqTemplFunctionality $functionality
     * @param TemplateGridFactory $templateGridFactory
     * @param LinearEqTemplateFormFactory $problemTemplateFormFactory
     * @param ConstHelper $constHelper
     * @param ISectionHelpModalFactory $sectionHelpModalFactory
     * @param ProblemTemplateStatus $problemTemplateStatus
     */
    public function __construct
    (
        Authorizator $authorizator, NewtonApiClient $newtonApiClient,
        HeaderBarFactory $headerBarFactory, SideBarFactory $sideBarFactory, FlashesTranslator $flashesTranslator,
        LinearEqTemplRepository $repository, LinearEqTemplFunctionality $functionality,
        TemplateGridFactory $templateGridFactory, LinearEqTemplateFormFactory $problemTemplateFormFactory,
        ConstHelper $constHelper, ISectionHelpModalFactory $sectionHelpModalFactory,
        ProblemTemplateStatus $problemTemplateStatus
    )
    {
        parent::__construct
        (
            $authorizator, $newtonApiClient,
            $headerBarFactory, $sideBarFactory, $flashesTranslator,
            $templateGridFactory,
            $constHelper, $sectionHelpModalFactory,
            $problemTemplateStatus
        );
        $this->repository = $repository;
        $this->functionality = $functionality;
        $this->problemTemplateFormFactory = $problemTemplateFormFactory;
        $this->typeId = $this->constHelper::LINEAR_EQ;
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