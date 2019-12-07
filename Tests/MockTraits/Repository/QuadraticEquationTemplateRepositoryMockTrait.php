<?php
/**
 * Created by PhpStorm.
 * User: wiedzmin
 * Date: 6.12.19
 * Time: 18:25
 */

namespace App\Tests\MockTraits\Repository;

use App\CoreModule\Model\Persistent\Entity\ProblemTemplate\QuadraticEquationTemplate;
use App\CoreModule\Model\Persistent\Repository\ProblemTemplate\QuadraticEquationTemplateRepository;
use PHPUnit\Framework\MockObject\MockObject;

/**
 * Trait QuadraticEquationTemplateRepositoryMockTrait
 * @package App\Tests\MockTraits\Repository
 */
trait QuadraticEquationTemplateRepositoryMockTrait
{
    /**
     * @var MockObject
     */
    protected $quadraticEquationTemplateRepositoryMock;

    /**
     * @var QuadraticEquationTemplate
     */
    protected $firstQuadraticEquationTemplate;

    /**
     * @var QuadraticEquationTemplate
     */
    protected $secondQuadraticEquationTemplate;

    protected function setUpQuadraticEquationTemplateRepositoryMock(): void
    {
        // Mock the QuadraticEquationTemplateRepository
        $this->quadraticEquationTemplateRepositoryMock = $this->getMockBuilder(QuadraticEquationTemplateRepository::class)
            ->setMethods(['find', 'getSequenceVal'])
            ->disableOriginalConstructor()
            ->getMock();

        // Create first QuadraticEquationTemplate
        $firstEquationTemplate = new QuadraticEquationTemplate();
        $firstEquationTemplate->setId(1);
        $firstEquationTemplate->setBody('TEST_QUADRATIC_EQUATION_TEMPLATE_FIRST');
        $firstEquationTemplate->setVariable('x');
        $this->firstQuadraticEquationTemplate = $firstEquationTemplate;

        $secondEquationTemplate = new QuadraticEquationTemplate();
        $secondEquationTemplate->setId(2);
        $secondEquationTemplate->setBody('TEST_QUADRATIC_EQUATION_TEMPLATE_SECOND');
        $secondEquationTemplate->setVariable('x');
        $this->secondQuadraticEquationTemplate = $secondEquationTemplate;

        // Set expected return values for repository find method
        $this->quadraticEquationTemplateRepositoryMock->method('find')
            ->willReturnCallback(static function ($arg) use (
                $firstEquationTemplate, $secondEquationTemplate
            ) {
                switch ($arg) {
                    case 1: return $firstEquationTemplate;
                    case 2: return $secondEquationTemplate;
                    default: return null;
                }
            });
    }
}