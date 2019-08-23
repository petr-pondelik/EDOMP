<?php
/**
 * Created by PhpStorm.
 * User: wiedzmin
 * Date: 21.7.19
 * Time: 18:06
 */

namespace App\Arguments;

use Nette\Utils\ArrayHash;

/**
 * Class ValidatorArgument
 * @package App\Arguments
 */
class ValidatorArgument
{
    public $data;

    /**
     * @var string
     */
    public $validationRule;

    /**
     * @var string
     */
    public $display;

    /**
     * ValidatorArgument constructor.
     * @param $data
     * @param string $validationRule
     * @param string|null $display
     */
    public function __construct($data, string $validationRule, string $display = null)
    {
        if(is_array($data)){
            $this->data = ArrayHash::from($data);
        }
        else{
            $this->data = $data;
        }
        $this->validationRule = $validationRule;
        $this->display = $display;
    }
}