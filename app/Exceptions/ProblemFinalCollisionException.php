<?php
/**
 * Created by PhpStorm.
 * User: wiedzmin
 * Date: 26.5.19
 * Time: 20:09
 */

namespace App\Exceptions;

use Throwable;

/**
 * Class ProblemFinalCollisionException
 * @package App\Exceptions
 */
class ProblemFinalCollisionException extends \Exception
{
    /**
     * ProblemFinalCollisionException constructor.
     * @param string $message
     * @param int $code
     * @param Throwable|null $previous
     */
    public function __construct(string $message = "", int $code = 602, Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}