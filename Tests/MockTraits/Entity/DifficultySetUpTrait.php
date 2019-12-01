<?php
/**
 * Created by PhpStorm.
 * User: wiedzmin
 * Date: 27.11.19
 * Time: 18:15
 */

namespace App\Tests\MockTraits\Entity;

use App\CoreModule\Model\Persistent\Entity\Difficulty;

/**
 * Class DifficultySetUpTrait
 * @package App\Tests\Traits
 */
trait DifficultySetUpTrait
{
    /**
     * @var Difficulty
     */
    protected $difficultyMock;

    protected function setUpDifficultyMock(): void
    {
        $this->difficultyMock = $this->getMockBuilder(Difficulty::class)->disableOriginalConstructor()->getMock();
    }
}