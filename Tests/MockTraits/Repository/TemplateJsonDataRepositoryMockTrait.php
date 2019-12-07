<?php
/**
 * Created by PhpStorm.
 * User: wiedzmin
 * Date: 1.12.19
 * Time: 23:55
 */

namespace App\Tests\MockTraits\Repository;

use App\CoreModule\Model\Persistent\Entity\TemplateJsonData;
use App\CoreModule\Model\Persistent\Repository\TemplateJsonDataRepository;
use Nette\Utils\DateTime;
use PHPUnit\Framework\MockObject\MockObject;

/**
 * Trait TemplateJsonDataRepositoryMockTrait
 * @package App\Tests\MockTraits\Repository
 */
trait TemplateJsonDataRepositoryMockTrait
{
    /**
     * @var MockObject
     */
    protected $templateJsonDataRepositoryMock;

    /**
     * @var TemplateJsonData
     */
    protected $firstTemplateJsonData;

    /**
     * @var TemplateJsonData
     */
    protected $secondTemplateJsonData;

    /**
     * @throws \Exception
     */
    protected function setUpTemplateJsonDataRepository(): void
    {
        $this->templateJsonDataRepositoryMock = $this->getMockBuilder(TemplateJsonDataRepository::class)
            ->setMethods(['find', 'findOneBy', 'findBy'])
            ->disableOriginalConstructor()
            ->getMock();

        // Create first TemplateJsonData
        $firstTemplateJsonData = new TemplateJsonData();
        $firstTemplateJsonData->setId(1);
        $firstTemplateJsonData->setTemplateId(1);
        $firstTemplateJsonData->setJsonData('[{\"p0\":0,\"p1\":0,\"p2\":0}]');
        $firstTemplateJsonData->setCreated(DateTime::from('2019-11-29 16:10:40'));
        $this->firstTemplateJsonData = $firstTemplateJsonData;

        // Create second TemplateJsonData
        $secondTemplateJsonData = new TemplateJsonData();
        $secondTemplateJsonData->setId(2);
        $secondTemplateJsonData->setTemplateId(2);
        $secondTemplateJsonData->setJsonData('[{\"p0\":0,\"p1\":0,\"p2\":0}]');
        $secondTemplateJsonData->setCreated(DateTime::from('2019-11-29 16:10:40'));
        $this->secondTemplateJsonData = $secondTemplateJsonData;

        // Set TemplateJsonDataRepository expected return values for find
        $this->templateJsonDataRepositoryMock->method('find')
            ->willReturnCallback(static function ($arg) use ($firstTemplateJsonData, $secondTemplateJsonData) {
                switch ($arg) {
                    case 1: return $firstTemplateJsonData;
                    case 2: return $secondTemplateJsonData;
                    default: return null;
                }
            });
    }
}