<?php
/**
 * Created by PhpStorm.
 * User: wiedzmin
 * Date: 25.5.19
 * Time: 10:49
 */

namespace App\CoreModule\Arguments;

/**
 * Class UserInformArgs
 * @package App\CoreModule\Arguments
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
     * @var string
     */
    public $component;

    /**
     * @var string
     */
    public $message;

    /**
     * UserInformArgs constructor.
     * @param string|null $operation
     * @param bool $ajax
     * @param string $type
     * @param \Exception|null $exception
     * @param bool $main
     * @param string|null $component
     * @param string|null $message
     */
    public function __construct
    (
        string $operation = null, bool $ajax = false, string $type = 'success', \Exception $exception = null,
        bool $main = false, string $component = null, string $message = null
    )
    {
        $this->operation = $operation;
        $this->ajax = $ajax;
        $this->type = $type;
        $this->exception = $exception;
        $this->main = $main;
        $this->component = $component;
        $this->message = $message;
    }
}