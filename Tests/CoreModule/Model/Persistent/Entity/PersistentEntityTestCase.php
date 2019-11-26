<?php
/**
 * Created by PhpStorm.
 * User: wiedzmin
 * Date: 24.11.19
 * Time: 21:35
 */

namespace App\Tests\CoreModule\Model\Persistent\Entity;


use App\Tests\EDOMPUnitTestCase;
use Symfony\Component\Validator\Validation;
use Symfony\Component\Validator\Validator\ValidatorInterface;

/**
 * Class PersistentEntityTestCase
 * @package App\Tests\CoreModule\Model\Persistent\Entity
 */
abstract class PersistentEntityTestCase extends EDOMPUnitTestCase
{
    /**
     * @var ValidatorInterface
     */
    protected $validator;

    protected function setUp(): void
    {
        parent::setUp();
        $this->validator = Validation::createValidatorBuilder()
            ->enableAnnotationMapping()
            ->getValidator();
    }

    abstract public function testValidState(): void;

    abstract public function testInvalidState(): void;
}