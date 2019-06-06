<?php
/**
 * Created by PhpStorm.
 * User: wiedzmin
 * Date: 6.6.19
 * Time: 12:01
 */

namespace App\AppTests\Model\Functionality;

use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

/**
 * Class FunctionalityTestCase
 * @package App\AppTests\Model\Functionality
 */
class FunctionalityTestCase extends TestCase
{
    /**
     * @var MockObject
     */
    protected $repositoryMock;

    protected $functionality;
}