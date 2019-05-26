<?php
/**
 * Created by PhpStorm.
 * User: wiedzmin
 * Date: 28.3.19
 * Time: 14:48
 */

namespace App\Exceptions;

use Nette\InvalidArgumentException;

/**
 * Class StringFormatException
 * @package App\Exceptions
 */
class StringFormatException extends InvalidArgumentException
{
    /**
     * StringFormatException constructor.
     * @param string $message
     * @param int $code
     * @param InvalidArgumentException|null $previous
     */
    public function __construct(string $message, int $code = 601, InvalidArgumentException $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}