<?php
/**
 * Created by PhpStorm.
 * User: wiedzmin
 * Date: 31.7.19
 * Time: 18:37
 */

namespace App\Arguments;

use Nette\Utils\ArrayHash;

/**
 * Class BodyArgument
 * @package App\Arguments
 */
class BodyArgument
{
    public $body;

    public $variable;

    /**
     * BodyArgument constructor.
     * @param ArrayHash $data
     */
    public function __construct(ArrayHash $data)
    {
        $this->body = $data->body;
        $this->variable = $data->variable;
    }
}