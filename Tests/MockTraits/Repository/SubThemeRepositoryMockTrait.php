<?php
/**
 * Created by PhpStorm.
 * User: wiedzmin
 * Date: 1.12.19
 * Time: 11:18
 */

namespace App\Tests\MockTraits\Repository;

use App\CoreModule\Model\Persistent\Entity\SubTheme;
use App\CoreModule\Model\Persistent\Repository\SubThemeRepository;
use Nette\Utils\DateTime;
use PHPUnit\Framework\MockObject\MockObject;

/**
 * Trait SubThemeRepositoryMockTrait
 * @package App\Tests\MockTraits\Repository
 */
trait SubThemeRepositoryMockTrait
{
    /**
     * @var MockObject
     */
    protected $subThemeRepositoryMock;

    /**
     * @var SubTheme
     */
    protected $firstSubTheme;

    /**
     * @var SubTheme
     */
    protected $secondSubTheme;

    /**
     * @throws \Exception
     */
    protected function setUpSubThemeRepositoryMock(): void
    {
        $this->subThemeRepositoryMock = $this->getMockBuilder(SubThemeRepository::class)
            ->setMethods(['find', 'findAssoc'])
            ->disableOriginalConstructor()
            ->getMock();

        // Create first SubTheme
        $firstSubTheme = new SubTheme();
        $firstSubTheme->setId(1);
        $firstSubTheme->setLabel('TEST_FIRST_THEME');
        $firstSubTheme->setCreated(DateTime::from('2019-11-29 16:10:40'));
        $this->firstSubTheme = $firstSubTheme;

        // Create second Theme
        $secondSubTheme = new SubTheme();
        $secondSubTheme->setId(2);
        $secondSubTheme->setLabel('TEST_SECOND_THEME');
        $secondSubTheme->setCreated(DateTime::from('2019-11-29 16:10:40'));
        $this->secondSubTheme = $secondSubTheme;

        // Set ThemeRepository expected return values for find
        $this->subThemeRepositoryMock->expects($this->any())
            ->method('find')
            ->willReturnCallback(static function ($arg) use ($firstSubTheme, $secondSubTheme) {
                switch ($arg) {
                    case 1: return $firstSubTheme;
                    case 2: return $secondSubTheme;
                    default: return null;
                }
            });

        // Set ThemeRepository expected return values for findAssoc
        $this->subThemeRepositoryMock->expects($this->any())
            ->method('findAssoc')
            ->willReturnCallback(static function ($arg) use ($firstSubTheme, $secondSubTheme) {
                switch ($arg) {
                    case []: return [
                        $firstSubTheme->getId() => $firstSubTheme,
                        $secondSubTheme->getId() => $secondSubTheme
                    ];
                    default: return null;
                }
            });
    }
}