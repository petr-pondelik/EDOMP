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
use App\Components\ProblemTemplateHelp\ProblemTemplateHelpFactory;
use App\Components\SideBar\SideBarFactory;
use App\Helpers\ConstHelper;
use App\Helpers\FlashesTranslator;
use App\Model\Entity\ProblemTemplate;
use App\Model\Functionality\QuadraticEqTemplFunctionality;
use App\Model\Repository\QuadraticEqTemplRepository;
use App\Services\Authorizator;
use App\Services\NewtonApiClient;
use Nette\ComponentModel\IComponent;

/**
 * Class QuadraticEqTemplatePresenter
 * @package App\AdminModule\Presenters
 */
class QuadraticEqTemplatePresenter extends ProblemTemplatePresenter
{
    /**
     * @var string
     */
    protected $type = 'QuadraticEqTemplate';

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
     * @param ProblemTemplateHelpFactory $problemTemplateHelpFactory
     */
    public function __construct
    (
        Authorizator $authorizator, NewtonApiClient $newtonApiClient,
        HeaderBarFactory $headerBarFactory, SideBarFactory $sideBarFactory, FlashesTranslator $flashesTranslator,
        QuadraticEqTemplRepository $repository, QuadraticEqTemplFunctionality $functionality,
        TemplateGridFactory $templateGridFactory, QuadraticEqTemplateFormFactory $problemTemplateFormFactory,
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