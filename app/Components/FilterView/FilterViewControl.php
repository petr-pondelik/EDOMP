<?php
/**
 * Created by PhpStorm.
 * User: wiedzmin
 * Date: 27.9.19
 * Time: 14:42
 */

namespace App\Components\FilterTable;


use App\Components\EDOMPControl;
use App\Helpers\FilterViewHelper;
use App\Model\Persistent\Entity\Filter;

/**
 * Class FilterTableControl
 * @package App\Components\FilterTable
 */
class FilterViewControl extends EDOMPControl
{
    /**
     * @var FilterViewHelper
     */
    protected $filterViewHelper;

    /**
     * @var Filter
     */
    protected $entity;

    /**
     * FilterViewControl constructor.
     * @param FilterViewHelper $filterViewHelper
     */
    public function __construct(FilterViewHelper $filterViewHelper)
    {
        parent::__construct();
        $this->filterViewHelper = $filterViewHelper;
    }

    public function render(): void
    {
        $this->template->entity = $this->entity;
        $this->template->processedFilters = $this->filterViewHelper->preprocessFilters($this->entity->getSelectedFilters());
        $this->template->render(__DIR__ . '/templates/default.latte');
    }

    /**
     * @return Filter
     */
    public function getEntity(): Filter
    {
        return $this->entity;
    }

    /**
     * @param Filter $entity
     */
    public function setEntity(Filter $entity): void
    {
        $this->entity = $entity;
    }
}