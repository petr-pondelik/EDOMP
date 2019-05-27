<?php
/**
 * Created by PhpStorm.
 * User: wiedzmin
 * Date: 27.5.19
 * Time: 23:35
 */

namespace App\Exceptions;

use Throwable;

/**
 * Class InvalidParameterException
 * @package App\Exceptions
 */
class InvalidParameterException extends \Exception
{
    /**
     * InvalidParameterException constructor.
     * @param string $message
     * @param int $code
     * @param Throwable|null $previous
     */
    public function __construct(string $message = "", int $code = 0, Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}