<?php
/**
 * Created by PhpStorm.
 * User: wiedzmin
 * Date: 29.9.19
 * Time: 21:26
 */

namespace App\Exceptions;

use Throwable;

/**
 * Class EDOMPException
 * @package App\Exceptions
 */
class EDOMPException extends \Exception
{
    /**
     * @var bool
     */
    protected $visible;

    /**
     * EDOMPException constructor.
     * @param string $message
     * @param bool $visible
     * @param int $code
     * @param Throwable|null $previous
     */
    public function __construct(string $message = '', bool $visible = true, int $code = 0, Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
        $this->visible = $visible;
    }

    /**
     * @return bool
     */
    public function isVisible(): bool
    {
        return $this->visible;
    }
}