<?php
/**
 * Created by PhpStorm.
 * User: wiedzmin
 * Date: 27.4.19
 * Time: 22:15
 */

namespace App\AdminModule\Presenters;

use App\Components\DataGrids\TemplateGridFactory;
use App\Components\Forms\TemplateFormFactory;
use App\Helpers\ConstHelper;
use App\Model\Functionality\LinearEqTemplFunctionality;
use App\Model\Repository\LinearEqTemplRepository;
use App\Model\Repository\ProblemTypeRepository;
use App\Service\MathService;
use App\Service\ValidationService;
use Nette\Application\UI\Form;
use Nette\Utils\ArrayHash;
use Ublaboo\DataGrid\DataGrid;

/**
 * Class LinearEqTemplatePresenter
 * @package App\AdminModule\Presenters
 */
class LinearEqTemplatePresenter extends ProblemTemplatePresenter
{
    /**
     * @var string
     */
    protected $type = "LinearEqTemplate";

    /**
     * LinearEqTemplatePresenter constructor.
     * @param LinearEqTemplRepository $repository
     * @param LinearEqTemplFunctionality $functionality
     * @param ProblemTypeRepository $problemTypeRepository
     * @param TemplateGridFactory $templateGridFactory
     * @param TemplateFormFactory $templateFormFactory
     * @param ValidationService $validationService
     * @param MathService $mathService
     * @param ConstHelper $constHelper
     */
    public function __construct
    (
        LinearEqTemplRepository $repository, LinearEqTemplFunctionality $functionality,
        ProblemTypeRepository $problemTypeRepository,
        TemplateGridFactory $templateGridFactory, TemplateFormFactory $templateFormFactory,
        ValidationService $validationService, MathService $mathService,
        ConstHelper $constHelper
    )
    {
        parent::__construct(
            $problemTypeRepository,
            $templateGridFactory, $templateFormFactory,
            $validationService, $mathService,
            $constHelper
        );
        $this->repository = $repository;
        $this->functionality = $functionality;
        $this->typeId = $this->constHelper::LINEAR_EQ;
    }

    /**
     * @return Form
     * @throws \Exception
     */
    public function createComponentTemplateCreateForm()
    {
        $form = $this->templateFormFactory->create(true, $this->typeId);
        $form->onValidate[] = [$this, "handleFormValidate"];
        $form->onSuccess[] = [$this, "handleCreateFormSuccess"];
        return $form;
    }

    /**
     * @param int $id
     * @throws \Nette\Application\AbortException
     */
    public function handleUpdate(int $id)
    {
        $this->redirect("edit", $id);
    }

}