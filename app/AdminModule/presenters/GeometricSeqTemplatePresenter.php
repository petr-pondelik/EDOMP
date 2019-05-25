<?php
/**
 * Created by PhpStorm.
 * User: wiedzmin
 * Date: 29.4.19
 * Time: 18:26
 */

namespace App\AdminModule\Presenters;

use App\Components\DataGrids\TemplateGridFactory;
use App\Components\Forms\ProblemTemplateForm\ProblemTemplateFormFactory;
use App\Components\HeaderBar\HeaderBarFactory;
use App\Components\SideBar\SideBarFactory;
use App\Helpers\ConstHelper;
use App\Helpers\FlashesTranslator;
use App\Model\Entity\ProblemTemplate;
use App\Model\Functionality\GeometricSeqTemplFunctionality;
use App\Model\Repository\GeometricSeqTemplRepository;
use App\Service\Authorizator;
use Nette\ComponentModel\IComponent;

/**
 * Class GeometricSeqTemplatePresenter
 * @package App\AdminModule\Presenters
 */
class GeometricSeqTemplatePresenter extends ProblemTemplatePresenter
{
    /**
     * @var string
     */
    protected $type = "GeometricSeqTemplate";

    /**
     * GeometricSeqTemplatePresenter constructor.
     * @param Authorizator $authorizator
     * @param HeaderBarFactory $headerBarFactory
     * @param SideBarFactory $sideBarFactory
     * @param FlashesTranslator $flashesTranslator
     * @param GeometricSeqTemplRepository $repository
     * @param GeometricSeqTemplFunctionality $functionality
     * @param TemplateGridFactory $templateGridFactory
     * @param ProblemTemplateFormFactory $problemTemplateFormFactory
     * @param ConstHelper $constHelper
     */
    public function __construct
    (
        Authorizator $authorizator,
        HeaderBarFactory $headerBarFactory, SideBarFactory $sideBarFactory, FlashesTranslator $flashesTranslator,
        GeometricSeqTemplRepository $repository, GeometricSeqTemplFunctionality $functionality,
        TemplateGridFactory $templateGridFactory, ProblemTemplateFormFactory $problemTemplateFormFactory,
        ConstHelper $constHelper
    )
    {
        parent::__construct
        (
            $authorizator,
            $headerBarFactory, $sideBarFactory, $flashesTranslator,
            $templateGridFactory, $problemTemplateFormFactory,
            $constHelper
        );
        $this->repository = $repository;
        $this->functionality = $functionality;
        $this->typeId = $this->constHelper::GEOMETRIC_SEQ;
    }

    /**
     * @param IComponent $form
     * @param ProblemTemplate $record
     */
    public function setDefaults(IComponent $form, ProblemTemplate $record): void
    {
        parent::setDefaults($form, $record);
        $form["variable"]->setDefaultValue($record->getVariable());
        $form["first_n"]->setDefaultValue($record->getFirstN());
    }
}