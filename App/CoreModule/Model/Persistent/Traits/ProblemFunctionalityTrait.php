<?php
/**
 * Created by PhpStorm.
 * User: wiedzmin
 * Date: 11.9.19
 * Time: 21:23
 */

namespace App\CoreModule\Model\Persistent\Traits;

use App\CoreModule\Model\Persistent\Repository\DifficultyRepository;
use App\CoreModule\Model\Persistent\Repository\ProblemConditionRepository;
use App\CoreModule\Model\Persistent\Repository\ProblemTypeRepository;
use App\CoreModule\Model\Persistent\Repository\SubThemeRepository;

/**
 * Trait ProblemFunctionalityTrait
 * @package App\CoreModule\Model\Persistent\Traits
 */
trait ProblemFunctionalityTrait
{
    /**
     * @var ProblemTypeRepository
     */
    protected $problemTypeRepository;

    /**
     * @var ProblemConditionRepository
     */
    protected $problemConditionRepository;

    /**
     * @var DifficultyRepository
     */
    protected $difficultyRepository;

    /**
     * @var SubThemeRepository
     */
    protected $subThemeRepository;

    /**
     * @param ProblemTypeRepository $problemTypeRepository
     * @param ProblemConditionRepository $problemConditionRepository
     * @param DifficultyRepository $difficultyRepository
     * @param SubThemeRepository $subThemeRepository
     */
    public function injectRepositories
    (
        ProblemTypeRepository $problemTypeRepository,
        ProblemConditionRepository $problemConditionRepository, DifficultyRepository $difficultyRepository,
        SubThemeRepository $subThemeRepository
    ): void
    {
        $this->problemTypeRepository = $problemTypeRepository;
        $this->problemConditionRepository = $problemConditionRepository;
        $this->difficultyRepository = $difficultyRepository;
        $this->subThemeRepository = $subThemeRepository;
    }
}