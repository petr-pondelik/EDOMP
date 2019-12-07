<?php
/**
 * Created by PhpStorm.
 * User: wiedzmin
 * Date: 6.12.19
 * Time: 17:16
 */

namespace App\Tests\MockTraits\Repository;

use App\CoreModule\Model\Persistent\Entity\ProblemTemplate\LinearEquationTemplate;
use App\CoreModule\Model\Persistent\Repository\ProblemTemplate\LinearEquationTemplateRepository;
use PHPUnit\Framework\MockObject\MockObject;

/**
 * Trait LinearEquationTemplateRepositoryMock
 * @package App\Tests\MockTraits\Repository
 */
trait LinearEquationTemplateRepositoryMock
{
    /**
     * @var MockObject
     */
    protected $linearEquationTemplateRepositoryMock;

    /**
     * @var LinearEquationTemplate
     */
    protected $firstLinearEquationTemplate;

    /**
     * @var LinearEquationTemplate
     */
    protected $secondLinearEquationTemplate;

    protected function setUpLinearEquationTemplateRepositoryMock(): void
    {
        // Mock the LinearEquationTemplateRepository
        $this->linearEquationTemplateRepositoryMock = $this->getMockBuilder(LinearEquationTemplateRepository::class)
            ->setMethods(['find', 'getSequenceVal'])
            ->disableOriginalConstructor()
            ->getMock();

        // Create first LinearEquationTemplate
        $firstLinearEquationTemplate = new LinearEquationTemplate();
        $firstLinearEquationTemplate->setId(1);
        $firstLinearEquationTemplate->setBody('TEST_LINEAR_EQUATION_TEMPLATE_FIRST');
        $firstLinearEquationTemplate->setVariable('x');
        $this->firstLinearEquationTemplate = $firstLinearEquationTemplate;

        $secondEquationTemplate = new LinearEquationTemplate();
        $secondEquationTemplate->setId(2);
        $secondEquationTemplate->setBody('TEST_LINEAR_EQUATION_TEMPLATE_SECOND');
        $secondEquationTemplate->setVariable('x');
        $this->secondLinearEquationTemplate = $secondEquationTemplate;

        // Set expected return values for repository find method
        $this->linearEquationTemplateRepositoryMock->method('find')
            ->willReturnCallback(static function ($arg) use (
                $firstLinearEquationTemplate, $secondEquationTemplate
            ) {
                switch ($arg) {
                    case 1: return $firstLinearEquationTemplate;
                    case 2: return $secondEquationTemplate;
                    default: return null;
                }
            });
    }
}