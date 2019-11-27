<?php
/**
 * Created by PhpStorm.
 * User: wiedzmin
 * Date: 24.11.19
 * Time: 21:35
 */

declare(strict_types = 1);

namespace App\Tests\CoreModule\Model\Persistent\Entity;


use App\CoreModule\Model\Persistent\Entity\BaseEntity;
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

    /**
     * @var array
     */
    protected $errorMessages = [];

    protected function setUp(): void
    {
        parent::setUp();
        $this->validator = Validation::createValidatorBuilder()
            ->enableAnnotationMapping()
            ->getValidator();
    }

    /**
     * @param BaseEntity $entity
     */
    protected function assertValidByValidator(BaseEntity $entity): void
    {
        $violations = $this->validator->validate($entity);
        $this->assertCount(0, $violations);
    }

    /**
     * @param BaseEntity $entity
     */
    protected function assertValidatorViolations(BaseEntity $entity): void
    {
        $violations = $this->validator->validate($entity);
        $this->assertCount(count($this->errorMessages), $violations);
        foreach ($violations as $key => $violation) {
            $this->assertEquals($this->errorMessages[$key], $violation->getMessage());
        }
    }

    abstract public function testValidState(): void;

    abstract public function testInvalidState(): void;
}