<?php
/**
 * Created by PhpStorm.
 * User: wiedzmin
 * Date: 14.7.19
 * Time: 18:15
 */

namespace App\Components\ProblemStack;


use App\Components\EDOMPControl;
use App\Model\Persistent\Entity\Problem;
use App\Model\Persistent\Repository\ProblemRepository;

/**
 * Class ProblemStackControl
 * @package App\Components\ProblemStack
 */
class ProblemStackControl extends EDOMPControl
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
        foreach ($selectedProblems as $key => $selectedProblem){
            if(array_key_exists($key, $problems)){
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
        if($selectedProblems){
            $this->problems = $this->filterStackBySelected($problems, $selectedProblems);
        }
        else{
            $this->problems = $problems;
        }
        $this->selectedProblems = $selectedProblems;
    }

    public function render(): void
    {
        $this->template->id = $this->id;
        $this->template->problems = $this->problems;
        $this->template->selectedProblems = $this->selectedProblems;
        $this->template->render(__DIR__ . '/templates/default.latte');
    }
}