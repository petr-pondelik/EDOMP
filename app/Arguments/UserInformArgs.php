<?php
/**
 * Created by PhpStorm.
 * User: wiedzmin
 * Date: 25.5.19
 * Time: 10:49
 */

namespace App\Arguments;

/**
 * Class UserInformArgs
 * @package App\Arguments
 */
class UserInformArgs
{
    /**
     * @var string
     */
    public $operation;

    /**
     * @var bool
     */
    public $ajax;

    /**
     * @var string
     */
    public $type;

    /**
     * @var \Exception|null
     */
    public $exception;

    /**
     * @var bool
     */
    public $main;

    /**
     * UserInformArgs constructor.
     * @param string|null $operation
     * @param bool $ajax
     * @param string $type
     * @param \Exception|null $exception
     * @param bool $main
     */
    public function __construct
    (
        string $operation = null, bool $ajax = false, string $type = 'success', \Exception $exception = null, bool $main = false
    )
    {
        $this->operation = $operation;
        $this->ajax = $ajax;
        $this->type = $type;
        $this->exception = $exception;
        $this->main = $main;
    }
}