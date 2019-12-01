<?php
/**
 * Created by PhpStorm.
 * User: wiedzmin
 * Date: 30.11.19
 * Time: 17:24
 */

namespace App\Tests\MockTraits\Repository;

use App\CoreModule\Model\Persistent\Entity\Theme;
use App\CoreModule\Model\Persistent\Repository\ThemeRepository;
use Nette\Utils\DateTime;
use PHPUnit\Framework\MockObject\MockObject;

/**
 * Trait ThemeRepositoryMockTrait
 * @package App\Tests\MockTraits\Repository
 */
trait ThemeRepositoryMockTrait
{
    /**
     * @var MockObject
     */
    protected $themeRepositoryMock;

    /**
     * @var Theme
     */
    protected $firstTheme;

    /**
     * @var Theme
     */
    protected $secondTheme;

    /**
     * @throws \Exception
     */
    protected function setUpThemeRepositoryMock(): void
    {
        $this->themeRepositoryMock = $this->getMockBuilder(ThemeRepository::class)
            ->setMethods(['find'])
            ->disableOriginalConstructor()
            ->getMock();

        // Create first Theme
        $firstTheme = new Theme();
        $firstTheme->setId(1);
        $firstTheme->setLabel('TEST_FIRST_THEME');
        $firstTheme->setCreated(DateTime::from('2019-11-29 16:10:40'));
        $this->firstTheme = $firstTheme;

        // Create second Theme
        $secondTheme = new Theme();
        $secondTheme->setId(2);
        $secondTheme->setLabel('TEST_SECOND_THEME');
        $this->secondTheme = $secondTheme;

        // Set ThemeRepository expected return values for find
        $this->themeRepositoryMock->expects($this->any())
            ->method('find')
            ->willReturnCallback(static function ($arg) use ($firstTheme, $secondTheme) {
                switch ($arg) {
                    case 1: return $firstTheme;
                    case 2: return $secondTheme;
                    default: return null;
                }
            });
    }
}