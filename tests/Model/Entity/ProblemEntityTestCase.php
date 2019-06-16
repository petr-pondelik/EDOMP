<?php
/**
 * Created by PhpStorm.
 * User: wiedzmin
 * Date: 6.6.19
 * Time: 9:16
 */

namespace Tests\Model\Entity;

use App\Model\Entity\Category;
use App\Model\Entity\Difficulty;
use App\Model\Entity\ProblemType;
use App\Model\Entity\SubCategory;

/**
 * Class ProblemEntityTestCase
 * @package Tests\Model\Entity
 */
abstract class ProblemEntityTestCase extends EntityTestCase
{
    /**
     * @var Category
     */
    protected $category;

    /**
     * @var Difficulty
     */
    protected $difficulty;

    /**
     * @var SubCategory
     */
    protected $subCategory;

    /**
     * @var ProblemType
     */
    protected $problemType;

    /**
     * @throws \Exception
     */
    public function setUp(): void
    {
        parent::setUp();

        $category = new Category();
        $category->setLabel('TEST_CATEGORY');
        $this->category = $category;

        $difficulty = new Difficulty();
        $difficulty->setLabel('TEST_DIFFICULTY');
        $this->difficulty = $difficulty;

        $subCategory = new SubCategory();
        $subCategory->setLabel('TEST_SUBCATEGORY');
        $subCategory->setCategory($category);
        $this->subCategory = $subCategory;

        $problemType = new ProblemType();
        $problemType->setLabel('TEST_PROBLEM_TYPE');
        $this->problemType = $problemType;
    }
}