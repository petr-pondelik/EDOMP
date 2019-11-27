<?php
/**
 * Created by PhpStorm.
 * User: wiedzmin
 * Date: 27.11.19
 * Time: 17:23
 */

declare(strict_types = 1);

namespace App\Tests\CoreModule\Model\Persistent\Entity;

use App\CoreModule\Model\Persistent\Entity\TemplateJsonData;
use App\Tests\Traits\ProblemConditionTypeSetUpTrait;

/**
 * Class TemplateJsonDataUnitTest
 * @package App\Tests\CoreModule\Model\Persistent\Entity
 */
class TemplateJsonDataUnitTest extends PersistentEntityTestCase
{
    use ProblemConditionTypeSetUpTrait;

    /**
     * @var array
     */
    protected $errorMessages = [
        0 => 'TemplateId can\'t be blank.'
    ];

    protected function setUp(): void
    {
        parent::setUp();
        $this->setUpProblemConditionTypeMock();
    }

    public function testValidState(): void
    {
        $entity = new TemplateJsonData();
        $jsonData = '[{\"p0\":-5},{\"p0\":-4},{\"p0\":-3},{\"p0\":-2},{\"p0\":-1},{\"p0\":0},{\"p0\":1},{\"p0\":2},{\"p0\":3}]';
        $templateId = 5;

        $this->assertInstanceOf(TemplateJsonData::class, $entity);

        $entity->setJsonData($jsonData);
        $entity->setTemplateId($templateId);
        $entity->setProblemConditionType($this->problemConditionTypeMock);

        $this->assertFalse($entity->isTeacherLevelSecured());
        $this->assertEquals($jsonData, $entity->getJsonData());
        $this->assertEquals($templateId, $entity->getTemplateId());
        $this->assertEquals($this->problemConditionTypeMock, $entity->getProblemConditionType());
        $this->assertValidByValidator($entity);
    }

    public function testInvalidState(): void
    {
        $entity = new TemplateJsonData();
        $this->assertValidatorViolations($entity);
    }
}