<?php
/**
 * Created by PhpStorm.
 * User: wiedzmin
 * Date: 1.12.19
 * Time: 18:10
 */

namespace App\Tests\MockTraits\Repository;

use App\CoreModule\Model\Persistent\Entity\Logo;
use App\CoreModule\Model\Persistent\Repository\LogoRepository;
use Nette\Utils\DateTime;
use PHPUnit\Framework\MockObject\MockObject;

/**
 * Trait LogoRepositoryMockTrait
 * @package App\Tests\MockTraits\Repository
 */
trait LogoRepositoryMockTrait
{
    /**
     * @var MockObject
     */
    protected $logoRepositoryMock;

    /**
     * @var Logo
     */
    protected $firstLogo;

    /**
     * @var Logo
     */
    protected $secondLogo;

    /**
     * @throws \Exception
     */
    protected function setUpLogoRepositoryMock(): void
    {
        $this->logoRepositoryMock = $this->getMockBuilder(LogoRepository::class)
            ->setMethods(['find'])
            ->disableOriginalConstructor()
            ->getMock();

        // Create first Logo
        $firstLogo = new Logo();
        $firstLogo->setId(1);
        $firstLogo->setLabel('TEST_FIRST_LOGO');
        $firstLogo->setCreated(DateTime::from('2019-11-29 16:10:40'));
        $this->firstLogo = $firstLogo;

        // Create second Logo
        $secondLogo = new Logo();
        $secondLogo->setId(2);
        $secondLogo->setLabel('TEST_SECOND_LOGO');
        $secondLogo->setCreated(DateTime::from('2019-11-29 16:10:40'));
        $this->secondLogo = $secondLogo;

        // Set LogoRepository expected return values for find
        $this->logoRepositoryMock->expects($this->any())
            ->method('find')
            ->willReturnCallback(static function ($arg) use ($firstLogo, $secondLogo) {
                switch ($arg) {
                    case 1: return $firstLogo;
                    case 2: return $secondLogo;
                    default: return null;
                }
            });
    }
}