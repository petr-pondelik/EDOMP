<?php
/**
 * Created by PhpStorm.
 * User: wiedzmin
 * Date: 1.12.19
 * Time: 12:14
 */

namespace App\Tests\MockTraits\Repository;

use App\CoreModule\Model\Persistent\Entity\Difficulty;
use App\CoreModule\Model\Persistent\Repository\DifficultyRepository;
use Nette\Utils\DateTime;
use PHPUnit\Framework\MockObject\MockObject;

/**
 * Trait DifficultyRepositoryMockTrait
 * @package App\Tests\MockTraits\Repository
 */
trait DifficultyRepositoryMockTrait
{
    /**
     * @var MockObject
     */
    protected $difficultyRepositoryMock;

    /**
     * @var Difficulty
     */
    protected $firstDifficulty;

    /**
     * @var Difficulty
     */
    protected $secondDifficulty;

    /**
     * @throws \Exception
     */
    protected function setUpDifficultyRepositoryMock(): void
    {
        $this->difficultyRepositoryMock = $this->getMockBuilder(DifficultyRepository::class)
            ->setMethods(['find', 'findAssoc'])
            ->disableOriginalConstructor()
            ->getMock();

        // Create first Difficulty
        $firstDifficulty = new Difficulty();
        $firstDifficulty->setId(1);
        $firstDifficulty->setLabel('TEST_FIRST_DIFFICULTY');
        $firstDifficulty->setCreated(DateTime::from('2019-11-29 16:10:40'));

        // Create second Difficulty
        $secondDifficulty = new Difficulty();
        $secondDifficulty->setId(2);
        $secondDifficulty->setLabel('TEST_SECOND_DIFFICULTY');
        $secondDifficulty->setCreated(DateTime::from('2019-11-29 16:10:40'));

        // Set DifficultyRepository expected return values for find
        $this->difficultyRepositoryMock->expects($this->any())
            ->method('find')
            ->willReturnCallback(static function ($arg) use ($firstDifficulty, $secondDifficulty) {
                switch ($arg) {
                    case 1: return $firstDifficulty;
                    case 2: return $secondDifficulty;
                    default: return null;
                }
            });

        // Set DifficultyRepository expected return values for findAssoc
        $this->difficultyRepositoryMock->expects($this->any())
            ->method('findAssoc')
            ->willReturnCallback(static function ($arg) use ($firstDifficulty, $secondDifficulty) {
                switch ($arg) {
                    case []: return [
                        $firstDifficulty->getId() => $firstDifficulty,
                        $secondDifficulty->getId() => $secondDifficulty
                    ];
                    default: return null;
                }
            });
    }
}