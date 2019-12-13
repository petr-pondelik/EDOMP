<?php
/**
 * Created by PhpStorm.
 * User: wiedzmin
 * Date: 14.7.19
 * Time: 18:15
 */

namespace App\TeacherModule\Components\ProblemStack;


use App\CoreModule\Components\EDOMPControl;
use App\CoreModule\Model\Persistent\Entity\Problem;
use App\CoreModule\Model\Persistent\Repository\ProblemRepository;

/**
 * Class ProblemStackControl
 * @package App\TeacherModule\Components\ProblemStack
 */
final class ProblemStackControl extends EDOMPControl
{
    /**
     * @var ProblemRepository
     */
    protected $problemRepository;

    /**
     * @var Problem[]
     */
    protected $problems;

    /**
     * @var Problem[]
     */
    protected $selectedProblems;

    /**
     * @var int
     */
    protected $id;

    /**
     * ProblemStackControl constructor.
     * @param ProblemRepository $problemRepository
     * @param int $id
     */
    public function __construct(ProblemRepository $problemRepository, int $id)
    {
        parent::__construct();
        $this->problemRepository = $problemRepository;
        $this->id = $id;
    }

    /**
     * @param array $problems
     * @param array $selectedProblems
     * @return array
     */
    public function filterStackBySelected(array $problems, array $selectedProblems): array
    {
        foreach ($selectedProblems as $key => $selectedProblem) {
            if (array_key_exists($key, $problems)) {
                unset($problems[$key]);
            }
        }
        return $problems;
    }

    /**
     * @param array $problems
     * @param array|null $selectedProblems
     */
    public function setProblems(array $problems, ?array $selectedProblems = null): void
    {
        if ($selectedProblems) {
            $this->problems = $this->filterStackBySelected($problems, $selectedProblems);
        } else {
            $this->problems = $problems;
        }
        $this->selectedProblems = $selectedProblems;
    }

    public function render(): void
    {
        bdump($this->problems);
        $this->template->id = $this->id;
        $this->template->problems = $this->problems;
        $this->template->selectedProblems = $this->selectedProblems;

        parent::render();
    }
}