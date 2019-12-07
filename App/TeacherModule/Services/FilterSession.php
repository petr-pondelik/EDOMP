<?php
/**
 * Created by PhpStorm.
 * User: wiedzmin
 * Date: 12.10.19
 * Time: 15:17
 */

namespace App\TeacherModule\Services;

use App\CoreModule\Interfaces\IEDOMPSession;
use Nette\Http\Session;
use Nette\Http\SessionSection;

/**
 * Class FilterSession
 * @package App\TeacherModule\Services
 */
final class FilterSession implements IEDOMPSession
{
    /**
     * @var SessionSection
     */
    protected $filterSession;

    /**
     * FilterSession constructor.
     * @param Session $session
     */
    public function __construct(Session $session)
    {
        $this->filterSession = $session->getSection('filterSession');
    }

    public function erase(): void
    {
        $this->filterSession->filters = [];
    }

    /**
     * @param array $filters
     */
    public function setFilters(array $filters): void
    {
        $this->filterSession->filters = $filters;
    }

    /**
     * @return array
     */
    public function getFilters(): array
    {
        return $this->filterSession->filters;
    }

    /**
     * @param int $problemKey
     * @return array|null
     */
    public function getProblemFilters(int $problemKey): ?array
    {
        if (!isset($this->filterSession->filters[$problemKey])) {
            return null;
        }
        return $this->filterSession->filters[$problemKey];
    }

    /**
     * @param int $problemKey
     * @param array $filter
     * @return bool
     */
    public function problemFilterDiffer(int $problemKey, array $filter): bool
    {
        if (!$storedFilter = $this->getProblemFilters($problemKey)) {
            return true;
        }
        return $storedFilter !== $filter;
    }
}