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
use App\Components\Forms\ProblemTemplateForm\ProblemTemplateFormFactory;
use App\Components\HeaderBar\HeaderBarFactory;
use App\Components\ProblemTemplateHelp\ProblemTemplateHelpFactory;
use App\Components\SideBar\SideBarFactory;
use App\Helpers\ConstHelper;
use App\Helpers\FlashesTranslator;
use App\Model\Entity\ProblemTemplate;
use App\Model\Functionality\LinearEqTemplFunctionality;
use App\Model\Repository\LinearEqTemplRepository;
use App\Services\Authorizator;
use App\Services\NewtonApiClient;
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
     * @param ProblemTemplateFormFactory $problemTemplateFormFactory
     * @param ConstHelper $constHelper
     * @param ProblemTemplateHelpFactory $problemTemplateHelpFactory
     */
    public function __construct
    (
        Authorizator $authorizator, NewtonApiClient $newtonApiClient,
        HeaderBarFactory $headerBarFactory, SideBarFactory $sideBarFactory, FlashesTranslator $flashesTranslator,
        LinearEqTemplRepository $repository, LinearEqTemplFunctionality $functionality,
        TemplateGridFactory $templateGridFactory, LinearEqTemplateFormFactory $problemTemplateFormFactory,
        ConstHelper $constHelper, ProblemTemplateHelpFactory $problemTemplateHelpFactory
    )
    {
        parent::__construct
        (
            $authorizator, $newtonApiClient,
            $headerBarFactory, $sideBarFactory, $flashesTranslator,
            $templateGridFactory,
            $constHelper, $problemTemplateHelpFactory
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