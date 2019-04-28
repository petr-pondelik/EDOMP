<?php
/**
 * Created by PhpStorm.
 * User: wiedzmin
 * Date: 28.4.19
 * Time: 9:40
 */

namespace App\AdminModule\Presenters;

use App\Components\DataGrids\TemplateGridFactory;
use App\Components\Forms\ProblemFormFactory;
use App\Helpers\ConstHelper;
use App\Model\Functionality\BaseFunctionality;
use App\Model\Repository\BaseRepository;
use Ublaboo\DataGrid\DataGrid;

/**
 * Class ProblemTemplatePresenter
 * @package App\AdminModule\Presenters
 */
abstract class ProblemTemplatePresenter extends AdminPresenter
{
    /**
     * @var BaseRepository
     */
    protected $repository;

    /**
     * @var BaseFunctionality
     */
    protected $functionality;

    /**
     * @var TemplateGridFactory
     */
    protected $templateGridFactory;

    /**
     * @var ProblemFormFactory
     */
    protected $problemFormFactory;

    /**
     * @var ConstHelper
     */
    protected $constHelper;

    /**
     * @var string
     */
    protected $type = "";

    /**
     * @var int
     */
    protected $typeId;

    /**
     * ProblemTemplatePresenter constructor.
     * @param TemplateGridFactory $templateGridFactory
     * @param ProblemFormFactory $problemFormFactory
     * @param ConstHelper $constHelper
     */
    public function __construct
    (
        TemplateGridFactory $templateGridFactory, ProblemFormFactory $problemFormFactory,
        ConstHelper $constHelper
    )
    {
        parent::__construct();
        $this->templateGridFactory = $templateGridFactory;
        $this->problemFormFactory = $problemFormFactory;
        $this->constHelper = $constHelper;
    }

    /**
     * @param $name
     * @return DataGrid
     * @throws \Ublaboo\DataGrid\Exception\DataGridException
     */
    public function createComponentTemplateGrid($name): DataGrid
    {
        $grid = $this->templateGridFactory->create($this, $name, $this->repository, $this->typeId);
        $grid->addAction('delete', '', 'delete!')
            ->setTemplate(__DIR__ . '/templates/' . $this->type . '/removeBtn.latte');
        return $grid;
    }

    /**
     * @param int $id
     */
    public function handleDelete(int $id)
    {
        try{
            $this->functionality->delete($id);
        } catch (\Exception $e){
            $this->flashMessage('Chyba při odstraňování šablony.', 'danger');
            $this->redrawControl('mainFlashesSnippet');
            return;
        }
        $this['problemGrid']->reload();
        $this->flashMessage('Šablona úspěšně odstraněna.', 'success');
        $this->redrawControl('mainFlashesSnippet');
    }
}