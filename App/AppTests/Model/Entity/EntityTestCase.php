<?php
/**
 * Created by PhpStorm.
 * User: wiedzmin
 * Date: 2.6.19
 * Time: 23:45
 */

namespace App\AppTests\Entity;


use PHPUnit\Framework\TestCase;
use Symfony\Component\Validator\Validation;
use Symfony\Component\Validator\Validator\ValidatorInterface;

/**
 * Class AppTestCase
 * @package AppTests
 */
abstract class EntityTestCase extends TestCase
{
    /**
     * @var ValidatorInterface
     */
    protected $validator;

    public function setUp(): void
    {
        parent::setUp();
        $this->validator = Validation::createValidatorBuilder()
            ->enableAnnotationMapping()
            ->getValidator();
    }
}