<?php
/**
 * Created by PhpStorm.
 * User: wiedzmin
 * Date: 4.6.19
 * Time: 22:07
 */

namespace AppTests\Model\Entity;


use App\AppTests\Entity\EntityTestCase;
use App\Model\Entity\Category;
use App\Model\Entity\Difficulty;
use App\Model\Entity\Group;
use App\Model\Entity\Logo;
use App\Model\Entity\ProblemFinal;
use App\Model\Entity\ProblemTestAssociation;
use App\Model\Entity\ProblemType;
use App\Model\Entity\SubCategory;
use App\Model\Entity\SuperGroup;
use App\Model\Entity\Test;

/**
 * Class ProblemTestAssociationTest
 * @package AppTests\Model\Entity
 */
class ProblemTestAssociationTest extends ProblemEntityTestCase
{
    /**
     * @var SuperGroup
     */
    protected $superGroup;

    /**
     * @var Group
     */
    protected $group;

    /**
     * @var Logo
     */
    protected $logo;

    /**
     * @var Test
     */
    protected $test;

    /**
     * @var ProblemFinal
     */
    protected $problemFinal;

    /**
     * @throws \Exception
     */
    public function setUp(): void
    {
        parent::setUp();

        $superGroup = new SuperGroup();
        $superGroup->setLabel('TEST_SUPER_GROUP');
        $this->superGroup = $superGroup;

        $group = new Group();
        $group->setLabel('TEST_GROUP');
        $group->setSuperGroup($superGroup);
        $this->group = $group;

        $logo = new Logo();
        $logo->setLabel('TEST_LOGO');
        $logo->setExtensionTmp('TEST_EXTENSION_TMP');
        $this->logo = $logo;

        $test = new Test();
        $test->setSchoolYear('2018/2019');
        $test->setTestNumber(1);
        $test->setTerm('1. pololetí');
        $test->addGroup($group);
        $test->setLogo($logo);
        $this->test = $test;

        $problemFinal = new ProblemFinal();
        $problemFinal->setBody('TEST_BODY');
        $problemFinal->setSubCategory($this->subCategory);
        $problemFinal->setDifficulty($this->difficulty);
        $problemFinal->setProblemType($this->problemType);
        $this->problemFinal = $problemFinal;
    }

    /**
     * @throws \Exception
     */
    public function testValues(): void
    {
        $entity = new ProblemTestAssociation();
        $entity->setTest($this->test);
        $entity->setVariant('A');
        $entity->setProblem($this->problemFinal);

        $this->assertEquals($entity->getTest(), $this->test);
        $this->assertEquals($entity->getVariant(), 'A');
        $this->assertEquals($entity->getProblem(), $this->problemFinal);
        $this->assertEquals($entity->getProblemTemplate(), null);
        $this->assertEquals($entity->getSuccessRate(), null);
    }

    /**
     * @throws \Exception
     */
    public function testCreateSuccess(): void
    {
        $entity = new ProblemTestAssociation();
        $entity->setTest($this->test);
        $entity->setVariant('A');
        $entity->setProblem($this->problemFinal);

        $this->assertInstanceOf(ProblemTestAssociation::class, $entity);
        $errors = $this->validator->validate($entity);
        $this->assertEquals($errors->count(), 0);
    }

    /**
     * @throws \Exception
     */
    public function testCreateError(): void
    {
        $errorMsgs = [
            0 => "Variant can't be blank.",
            1 => "ProblemFinal can't be blank.",
            2 => "Test can't be blank."
        ];

        $entity = new ProblemTestAssociation();

        $this->assertInstanceOf(ProblemTestAssociation::class, $entity);
        $errors = $this->validator->validate($entity);
        $this->assertEquals($errors->count(), 3);

        foreach ($errors as $key => $error){
            $this->assertEquals($error->getMessage(), $errorMsgs[$key]);
        }
    }
}