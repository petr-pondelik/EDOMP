<?php
/**
 * Created by PhpStorm.
 * User: wiedzmin
 * Date: 27.4.19
 * Time: 22:15
 */

namespace App\AdminModule\Presenters;

use App\Components\DataGrids\TemplateGridFactory;
use App\Components\Forms\ProblemFormFactory;
use App\Helpers\ConstHelper;
use App\Model\Repository\LinearEqTemplRepository;
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
    protected $type = "linearEqTemplate";

    public function __construct
    (
        LinearEqTemplRepository $repository,
        TemplateGridFactory $templateGridFactory, ProblemFormFactory $problemFormFactory,
        ConstHelper $constHelper
    )
    {
        parent::__construct($templateGridFactory, $problemFormFactory, $constHelper);
        $this->repository = $repository;
        $this->typeId = $this->constHelper::LINEAR_EQ;
    }

    public function createComponentTemplateGrid($name): DataGrid
    {
        $grid = parent::createComponentTemplateGrid($name);
        return $grid;
    }

    public function createComponentCreateForm()
    {
        $form = $this->problemFormFactory->create(true, $this->typeId);
        return $form;
    }
}