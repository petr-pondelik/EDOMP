<?php
/**
 * Created by PhpStorm.
 * User: wiedzmin
 * Date: 28.11.19
 * Time: 21:22
 */

namespace App\Tests\CoreModule\Model\Persistent\Repository;

use App\CoreModule\Model\Persistent\Entity\Difficulty;
use App\CoreModule\Model\Persistent\Repository\DifficultyRepository;
use Nette\Utils\DateTime;

/**
 * Class DifficultyRepositoryIntegrationTest
 * @package App\Tests\CoreModule\Model\Persistent\Repository
 */
final class DifficultyRepositoryIntegrationTest extends RepositoryIntegrationTestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        $this->repository = $this->container->getByType(DifficultyRepository::class);
    }

    /**
     * @throws \Exception
     */
    public function testFindAll(): void
    {
        $easyDifficulty = new Difficulty();
        $easyDifficulty->setId(1);
        $easyDifficulty->setCreated(DateTime::from('2019-02-17 10:29:19'));
        $easyDifficulty->setLabel('Lehká');

        $mediumDifficulty = new Difficulty();
        $mediumDifficulty->setId(2);
        $mediumDifficulty->setCreated(DateTime::from('2019-02-17 10:29:19'));
        $mediumDifficulty->setLabel('Střední');

        $hardDifficulty = new Difficulty();
        $hardDifficulty->setId(3);
        $hardDifficulty->setCreated(DateTime::from('2019-02-17 10:29:19'));
        $hardDifficulty->setLabel('Těžká');

        $expected = [$easyDifficulty, $mediumDifficulty, $hardDifficulty];
        $found = $this->repository->findAll();
        $this->assertCount(3, $found);
        $this->assertEquals($expected, $found);
    }
}