<?php
/**
 * Created by PhpStorm.
 * User: wiedzmin
 * Date: 22.11.19
 * Time: 10:51
 */

namespace App\CoreModule\Services;

use App\CoreModule\Interfaces\IGenerator;
use Nette\Utils\Random;

/**
 * Class Generator
 * @package App\CoreModule\Services
 */
class PasswordGenerator implements IGenerator
{
    /**
     * @return string
     */
    public function generate(): string
    {
        return Random::generate(8);
    }
}