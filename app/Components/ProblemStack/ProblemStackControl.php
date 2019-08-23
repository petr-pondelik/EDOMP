<?php
/**
 * Created by PhpStorm.
 * User: wiedzmin
 * Date: 14.7.19
 * Time: 18:15
 */

namespace App\Components\ProblemStack;


use App\Model\Persistent\Entity\Problem;
use App\Model\Persistent\Repository\ProblemRepository;
use Nette\Application\UI\Control;

/**
 * Class ProblemStackControl
 * @package App\Components\ProblemStack
 */
class ProblemStackControl extends Control
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
     * ProblemStackControl constructor.
     * @param ProblemRepository $problemRepository
     * @throws \Exception
     */
    public function __construct(ProblemRepository $problemRepository)
    {
        parent::__construct();
        $this->problemRepository = $problemRepository;

        $this->problems = $problemRepository->findAssoc([], 'id');
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
     * @param array $selectedProblems
     */
    public function setProblems(array $problems, array $selectedProblems): void
    {
        $this->problems = $this->filterStackBySelected($problems, $selectedProblems);
        $this->selectedProblems = $selectedProblems;
    }

    public function render(): void
    {
        $this->template->problems = $this->problems;
        $this->template->selectedProblems = $this->selectedProblems;
        $this->template->render(__DIR__ . '/templates/default.latte');
    }
}