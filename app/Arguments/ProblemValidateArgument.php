<?php
/**
 * Created by PhpStorm.
 * User: wiedzmin
 * Date: 25.7.19
 * Time: 7:53
 */

namespace App\Arguments;

use Nette\Utils\ArrayHash;

/**
 * Class ProblemValidateArgument
 * @package App\Arguments
 */
abstract class ProblemValidateArgument
{
    /**
     * @var int
     */
    public $templateId;

    /**
     * ProblemValidateArgument constructor.
     * @param ArrayHash $data
     */
    public function __construct(ArrayHash $data)
    {
        //bdump('PROBLEM VALIDATE ARGUMENT CONSTRUCTOR');
        //bdump($data);
        $this->templateId = $data->templateId ?? null;
    }
}