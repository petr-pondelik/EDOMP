<?php
/**
 * Created by PhpStorm.
 * User: wiedzmin
 * Date: 29.4.19
 * Time: 18:26
 */

namespace App\AdminModule\Presenters;

use App\Components\DataGrids\TemplateGridFactory;
use App\Components\HeaderBar\HeaderBarFactory;
use App\Components\SectionHelpModal\ISectionHelpModalFactory;
use App\Components\SideBar\SideBarFactory;
use App\Helpers\ConstHelper;
use App\Helpers\FlashesTranslator;
use App\Model\Persistent\Entity\ProblemTemplate;
use App\Model\Persistent\Functionality\GeometricSeqTemplFunctionality;
use App\Model\Persistent\Repository\GeometricSeqTemplRepository;
use App\Services\Authorizator;
use App\Services\NewtonApiClient;
use Nette\ComponentModel\IComponent;
use ProblemTemplateForm\GeometricSeqTemplateForm\GeometricSeqTemplateFormFactory;

/**
 * Class GeometricSeqTemplatePresenter
 * @package App\AdminModule\Presenters
 */
class GeometricSeqTemplatePresenter extends ProblemTemplatePresenter
{
    /**
     * GeometricSeqTemplatePresenter constructor.
     * @param Authorizator $authorizator
     * @param NewtonApiClient $newtonApiClient
     * @param HeaderBarFactory $headerBarFactory
     * @param SideBarFactory $sideBarFactory
     * @param FlashesTranslator $flashesTranslator
     * @param GeometricSeqTemplRepository $repository
     * @param GeometricSeqTemplFunctionality $functionality
     * @param TemplateGridFactory $templateGridFactory
     * @param GeometricSeqTemplateFormFactory $problemTemplateFormFactory
     * @param ConstHelper $constHelper
     * @param ISectionHelpModalFactory $sectionHelpModalFactory
     */
    public function __construct
    (
        Authorizator $authorizator, NewtonApiClient $newtonApiClient,
        HeaderBarFactory $headerBarFactory, SideBarFactory $sideBarFactory, FlashesTranslator $flashesTranslator,
        GeometricSeqTemplRepository $repository, GeometricSeqTemplFunctionality $functionality,
        TemplateGridFactory $templateGridFactory, GeometricSeqTemplateFormFactory $problemTemplateFormFactory,
        ConstHelper $constHelper, ISectionHelpModalFactory $sectionHelpModalFactory
    )
    {
        parent::__construct
        (
            $authorizator, $newtonApiClient,
            $headerBarFactory, $sideBarFactory, $flashesTranslator,
            $templateGridFactory,
            $constHelper, $sectionHelpModalFactory
        );
        $this->repository = $repository;
        $this->functionality = $functionality;
        $this->problemTemplateFormFactory = $problemTemplateFormFactory;
        $this->typeId = $this->constHelper::GEOMETRIC_SEQ;
    }

    /**
     * @param IComponent $form
     * @param ProblemTemplate $record
     */
    public function setDefaults(IComponent $form, ProblemTemplate $record): void
    {
        parent::setDefaults($form, $record);
        $form['variable']->setDefaultValue($record->getVariable());
        $form['firstN']->setDefaultValue($record->getFirstN());
    }
}