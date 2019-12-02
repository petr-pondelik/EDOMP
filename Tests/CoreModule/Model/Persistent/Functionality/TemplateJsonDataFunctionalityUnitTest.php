<?php
/**
 * Created by PhpStorm.
 * User: wiedzmin
 * Date: 1.12.19
 * Time: 23:52
 */

namespace App\Tests\CoreModule\Model\Persistent\Functionality;

use App\CoreModule\Model\Persistent\Entity\TemplateJsonData;
use App\CoreModule\Model\Persistent\Functionality\TemplateJsonDataFunctionality;
use App\Tests\MockTraits\Repository\ProblemConditionTypeRepositoryMockTrait;
use App\Tests\MockTraits\Repository\ProblemTemplateRepositoryMockTrait;
use App\Tests\MockTraits\Repository\TemplateJsonDataRepositoryMockTrait;
use Nette\Utils\ArrayHash;
use Nette\Utils\DateTime;

/**
 * Class TemplateJsonDataFunctionalityUnitTest
 * @package App\Tests\CoreModule\Model\Persistent\Functionality
 */
final class TemplateJsonDataFunctionalityUnitTest extends FunctionalityUnitTestCase
{
    use TemplateJsonDataRepositoryMockTrait;
    use ProblemTemplateRepositoryMockTrait;
    use ProblemConditionTypeRepositoryMockTrait;

    /**
     * @var TemplateJsonDataFunctionality
     */
    protected $functionality;

    /**
     * @throws \Exception
     */
    protected function setUp(): void
    {
        parent::setUp();
        $this->setUpTemplateJsonDataRepository();
        $this->setUpProblemTemplateRepositoryMock();
        $this->setUpProblemConditionTypeRepositoryMock();
        $this->functionality = new TemplateJsonDataFunctionality(
            $this->em, $this->templateJsonDataRepositoryMock, $this->problemTemplateRepositoryMock,
            $this->problemConditionTypeRepositoryMock
        );

        $firstTemplateJsonData = $this->firstTemplateJsonData;
        $firstTemplateJsonData->setProblemConditionType($this->firstProblemConditionType);
        $firstTemplateJsonData->setTemplateId(1);

        $this->templateJsonDataRepositoryMock->method('findOneBy')
            ->willReturnCallback(static function ($arg) use ($firstTemplateJsonData) {
                switch ($arg) {
                    case [
                        'templateId' => 1,
                        'problemConditionType' => 1
                    ]: return $firstTemplateJsonData;
                    default: return null;
                }
            });
    }

    /**
     * @throws \App\CoreModule\Exceptions\EntityException
     * @throws \Doctrine\ORM\EntityNotFoundException
     */
    public function testCreate(): void
    {
        // Data for TemplateJsonData create
        $data = ArrayHash::from([
            'jsonData' => '[{\"p0\":0,\"p1\":0,\"p2\":0}]',
            'created' => $this->dateTimeStr
        ]);

        $expected = new TemplateJsonData();
        $expected->setJsonData($data['jsonData']);
        $expected->setTemplateId(1);
        $expected->setCreated(DateTime::from($this->dateTimeStr));

        // Create TemplateJsonData and test it against expected
        $created = $this->functionality->create($data, false, 1);
        $this->assertEquals($expected, $created);

        $this->functionality->create($data, false, 1, 1);
        $this->assertEquals($data['jsonData'], $this->firstTemplateJsonData->getJsonData());
    }

    public function testUpdate(): void
    {
        $this->assertNull($this->functionality->update(1, []));
    }
}