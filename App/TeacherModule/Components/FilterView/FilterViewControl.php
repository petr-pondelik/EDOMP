<?php
/**
 * Created by PhpStorm.
 * User: wiedzmin
 * Date: 27.9.19
 * Time: 14:42
 */

namespace App\TeacherModule\Components\FilterView;


use App\CoreModule\Components\EDOMPControl;
use App\CoreModule\Model\Persistent\Entity\Filter;
use App\TeacherModule\Helpers\FilterViewHelper;

/**
 * Class FilterTableControl
 * @package App\TeacherModule\Components\FilterView
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
        parent::render();
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