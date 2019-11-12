<?php
/**
 * Created by PhpStorm.
 * User: wiedzmin
 * Date: 26.5.19
 * Time: 13:04
 */

namespace App\StudentModule\Components\Forms\ProblemFilterForm;


use App\CoreModule\Components\Forms\IFormFactory;

/**
 * Interface IProblemFilterFormFactory
 * @package App\StudentModule\Components\Forms\ProblemFilterForm
 */
interface IProblemFilterFormFactory extends IFormFactory
{
    /**
     * @param int|null $categoryId
     * @return ProblemFilterFormControl
     */
    public function create(int $categoryId = null): ProblemFilterFormControl;
}