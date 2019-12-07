<?php
/**
 * Created by PhpStorm.
 * User: wiedzmin
 * Date: 7.12.19
 * Time: 11:33
 */

namespace App\Tests\MockTraits\Repository;

use App\CoreModule\Model\Persistent\Entity\ProblemTemplate\GeometricSequenceTemplate;
use App\CoreModule\Model\Persistent\Repository\ProblemTemplate\GeometricSequenceTemplateRepository;
use PHPUnit\Framework\MockObject\MockObject;

/**
 * Trait GeometricSequenceTemplateRepositoryMockTrait
 * @package App\Tests\MockTraits\Repository
 */
trait GeometricSequenceTemplateRepositoryMockTrait
{
    /**
     * @var MockObject
     */
    protected $geometricSequenceTemplateRepositoryMock;

    /**
     * @var GeometricSequenceTemplate
     */
    protected $firstGeometricSequenceTemplate;

    /**
     * @var GeometricSequenceTemplate
     */
    protected $secondGeometricSequenceTemplate;

    protected function setUpGeometricSequenceTemplateRepositoryMock(): void
    {
        // Mock the GeometricSequenceTemplateRepository
        $this->geometricSequenceTemplateRepositoryMock = $this->getMockBuilder(GeometricSequenceTemplateRepository::class)
            ->setMethods(['find', 'getSequenceVal'])
            ->disableOriginalConstructor()
            ->getMock();

        // Create first GeometricSequenceTemplate
        $firstGeometricSequenceTemplate = new GeometricSequenceTemplate();
        $firstGeometricSequenceTemplate->setId(1);
        $firstGeometricSequenceTemplate->setBody('TEST_ARITHMETIC_SEQUENCE_TEMPLATE_FIRST');
        $firstGeometricSequenceTemplate->setIndexVariable('n');
        $firstGeometricSequenceTemplate->setFirstN(5);
        $this->firstGeometricSequenceTemplate = $firstGeometricSequenceTemplate;

        $secondGeometricSequenceTemplate = new GeometricSequenceTemplate();
        $secondGeometricSequenceTemplate->setId(2);
        $secondGeometricSequenceTemplate->setBody('TEST_ARITHMETIC_SEQUENCE_TEMPLATE_SECOND');
        $secondGeometricSequenceTemplate->setIndexVariable('m');
        $secondGeometricSequenceTemplate->setFirstN(10);
        $this->secondGeometricSequenceTemplate = $secondGeometricSequenceTemplate;

        // Set expected return values for repository find method
        $this->geometricSequenceTemplateRepositoryMock->method('find')
            ->willReturnCallback(static function ($arg) use (
                $firstGeometricSequenceTemplate, $secondGeometricSequenceTemplate
            ) {
                switch ($arg) {
                    case 1: return $firstGeometricSequenceTemplate;
                    case 2: return $secondGeometricSequenceTemplate;
                    default: return null;
                }
            });
    }
}