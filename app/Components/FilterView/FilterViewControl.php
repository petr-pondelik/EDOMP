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
    protected $filter;

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
        $this->template->filter = $this->filter;
        $this->template->render(__DIR__ . '/templates/default.latte');
    }

    /**
     * @return Filter
     */
    public function getFilter(): Filter
    {
        return $this->filter;
    }

    /**
     * @param Filter $filter
     */
    public function setFilter(Filter $filter): void
    {
        $filter->setSelectedFilters($this->filterViewHelper->preprocessFilters($filter->getSelectedFilters()));
        $this->filter = $filter;
    }
}