<?php
/**
 * Created by PhpStorm.
 * User: wiedzmin
 * Date: 26.5.19
 * Time: 13:04
 */

namespace App\Components\Forms\ProblemFilterForm;


use App\Components\Forms\FormFactory;
use App\Model\Repository\DifficultyRepository;
use App\Model\Repository\SubCategoryRepository;
use App\Services\Validator;

/**
 * Class ProblemFilterFormFactory
 * @package App\Components\Forms\ProblemFilterForm
 */
class ProblemFilterFormFactory extends FormFactory
{
    /**
     * @var SubCategoryRepository
     */
    protected $subCategoryRepository;

    /**
     * @var DifficultyRepository
     */
    protected $difficultyRepository;

    /**
     * ProblemFilterFormFactory constructor.
     * @param Validator $validator
     * @param SubCategoryRepository $subCategoryRepository
     * @param DifficultyRepository $difficultyRepository
     */
    public function __construct
    (
        Validator $validator,
        SubCategoryRepository $subCategoryRepository, DifficultyRepository $difficultyRepository
    )
    {
        parent::__construct($validator);
        $this->subCategoryRepository = $subCategoryRepository;
        $this->difficultyRepository = $difficultyRepository;
    }

    /**
     * @param int $categoryId
     * @return ProblemFilterFormControl
     */
    public function create(int $categoryId): ProblemFilterFormControl
    {
        return new ProblemFilterFormControl(
            $this->validator, $this->subCategoryRepository, $this->difficultyRepository, $categoryId
        );
    }
}