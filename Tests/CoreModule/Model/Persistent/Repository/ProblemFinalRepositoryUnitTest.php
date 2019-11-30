<?php
/**
 * Created by PhpStorm.
 * User: wiedzmin
 * Date: 30.11.19
 * Time: 11:27
 */

namespace App\Tests\CoreModule\Model\Persistent\Repository;

use App\CoreModule\Model\Persistent\Entity\ProblemFinal;
use App\CoreModule\Model\Persistent\Repository\ProblemFinalRepository;

/**
 * Class ProblemFinalRepositoryUnitTest
 * @package App\Tests\CoreModule\Model\Persistent\Repository
 */
final class ProblemFinalRepositoryUnitTest extends SecuredRepositoryTestCase
{
    /**
     * @var ProblemFinalRepository
     */
    protected $repository;

    protected function setUp(): void
    {
        parent::setUp();
        $this->repository = $this->container->getByType(ProblemFinalRepository::class);
    }

    public function testFind(): void
    {
        /** @var ProblemFinal $found */
        $found = $this->repository->find(31);
        $this->assertInstanceOf(ProblemFinal::class, $found);
        $this->assertEquals(31, $found->getId());
        $this->assertEquals('$$ 5 x = 15 - 4 + 2 $$', $found->getBody());
    }

    /**
     * @throws \Doctrine\ORM\Query\QueryException
     * @throws \Nette\Security\AuthenticationException
     */
    public function testFindAllowed(): void
    {
        $this->user->login('admin', '12345678');
        /** @var ProblemFinal[] $found */
        $found = $this->repository->findAllowed($this->user);
        $this->assertCount(11, $found);
        foreach ($found as $key => $item) {
            $this->assertInstanceOf(ProblemFinal::class, $item);
        }
        $this->user->logout(true);

        $this->user->login('jkohneke0@nba.com', '12345678');
        /** Test[] $found */
        $found = $this->repository->findAllowed($this->user);
        $this->assertCount(10, $found);
        foreach ($found as $key => $item) {
            $this->assertInstanceOf(ProblemFinal::class, $item);
        }
        $this->user->logout(true);

        $this->user->login('mhazzard1@wiley.com', '12345678');
        /** Test[] $found */
        $found = $this->repository->findAllowed($this->user);
        $this->assertCount(1, $found);
        foreach ($found as $key => $item) {
            $this->assertInstanceOf(ProblemFinal::class, $item);
        }
        $this->user->logout(true);
    }

    public function testGetStudentFiltered(): void
    {
        $expected = [
            '$$ 5 x = 15 - 4 + 2 $$',
            '$$ x - 1 = 4 $$',
            '$$ \frac{ -2 x}{ -1 } + \frac{ 0 x}{ 2 } = 1 $$',
            '$$ \frac{ -2 x}{ -2 } + \frac{ 0 x}{ -2 } = 2 $$',
            '$$ 1 = \frac{x - 1 + 4}{x^2 + x} + \frac{ 3 }{x} $$',
            '$$ -5 x^2 + x + 5 = 4 $$',
            '$$ 1 = \frac{x - 2 + 4}{x^2 + x} + \frac{ -1 }{x} $$'
        ];
        /** @var ProblemFinal[] $found */
        $found = $this->repository->getStudentFiltered(1, 10, 0, []);
        $this->assertCount(count($expected), $found);
        foreach ($found as $key => $item) {
            $this->assertInstanceOf(ProblemFinal::class, $item);
            $this->assertEquals($expected[$key], (string)$item);
        }

        $expected = [
            '$$ 5 x = 15 - 4 + 2 $$',
            '$$ x - 1 = 4 $$',
            '$$ -5 x^2 + x + 5 = 4 $$',
        ];
        /** @var ProblemFinal[] $found */
        $found = $this->repository->getStudentFiltered(1, 10, 0, ['difficulty' => [1]]);
        $this->assertCount(count($expected), $found);
        foreach ($found as $key => $item) {
            $this->assertInstanceOf(ProblemFinal::class, $item);
            $this->assertEquals($expected[$key], (string)$item);
        }

        $expected = [
            '$$ \frac{ -2 x}{ -1 } + \frac{ 0 x}{ 2 } = 1 $$',
            '$$ \frac{ -2 x}{ -2 } + \frac{ 0 x}{ -2 } = 2 $$',
            '$$ 1 = \frac{x - 1 + 4}{x^2 + x} + \frac{ 3 }{x} $$',
            '$$ 1 = \frac{x - 2 + 4}{x^2 + x} + \frac{ -1 }{x} $$'
        ];
        /** @var ProblemFinal[] $found */
        $found = $this->repository->getStudentFiltered(1, 10, 0, ['difficulty' => [2]]);
        $this->assertCount(count($expected), $found);
        foreach ($found as $key => $item) {
            $this->assertInstanceOf(ProblemFinal::class, $item);
            $this->assertEquals($expected[$key], (string)$item);
        }

        $expected = [
            '$$ 5 x = 15 - 4 + 2 $$',
            '$$ x - 1 = 4 $$',
            '$$ \frac{ -2 x}{ -1 } + \frac{ 0 x}{ 2 } = 1 $$',
            '$$ \frac{ -2 x}{ -2 } + \frac{ 0 x}{ -2 } = 2 $$',
            '$$ 1 = \frac{x - 1 + 4}{x^2 + x} + \frac{ 3 }{x} $$',
            '$$ -5 x^2 + x + 5 = 4 $$',
            '$$ 1 = \frac{x - 2 + 4}{x^2 + x} + \frac{ -1 }{x} $$'
        ];
        /** @var ProblemFinal[] $found */
        $found = $this->repository->getStudentFiltered(1, 10, 0, ['sort_by_difficulty' => 0]);
        $this->assertCount(count($expected), $found);
        foreach ($found as $key => $item) {
            $this->assertInstanceOf(ProblemFinal::class, $item);
            $this->assertEquals($expected[$key], (string)$item);
        }

        $expected = [
            '$$ 5 x = 15 - 4 + 2 $$',
            '$$ x - 1 = 4 $$',
            '$$ -5 x^2 + x + 5 = 4 $$',
            '$$ \frac{ -2 x}{ -1 } + \frac{ 0 x}{ 2 } = 1 $$',
            '$$ \frac{ -2 x}{ -2 } + \frac{ 0 x}{ -2 } = 2 $$',
            '$$ 1 = \frac{x - 1 + 4}{x^2 + x} + \frac{ 3 }{x} $$',
            '$$ 1 = \frac{x - 2 + 4}{x^2 + x} + \frac{ -1 }{x} $$'
        ];
        /** @var ProblemFinal[] $found */
        $found = $this->repository->getStudentFiltered(1, 10, 0, ['sort_by_difficulty' => 1]);
        $this->assertCount(count($expected), $found);
        foreach ($found as $key => $item) {
            $this->assertInstanceOf(ProblemFinal::class, $item);
            $this->assertEquals($expected[$key], (string)$item);
        }

        $expected = [
            '$$ \frac{ -2 x}{ -1 } + \frac{ 0 x}{ 2 } = 1 $$',
            '$$ \frac{ -2 x}{ -2 } + \frac{ 0 x}{ -2 } = 2 $$',
            '$$ 1 = \frac{x - 1 + 4}{x^2 + x} + \frac{ 3 }{x} $$',
            '$$ 1 = \frac{x - 2 + 4}{x^2 + x} + \frac{ -1 }{x} $$',
            '$$ 5 x = 15 - 4 + 2 $$',
            '$$ x - 1 = 4 $$',
            '$$ -5 x^2 + x + 5 = 4 $$',
        ];
        /** @var ProblemFinal[] $found */
        $found = $this->repository->getStudentFiltered(1, 10, 0, ['sort_by_difficulty' => 2]);
        $this->assertCount(count($expected), $found);
        foreach ($found as $key => $item) {
            $this->assertInstanceOf(ProblemFinal::class, $item);
            $this->assertEquals($expected[$key], (string)$item);
        }

        $expected = [
            '$$ 5 x = 15 - 4 + 2 $$',
            '$$ x - 1 = 4 $$',
            '$$ \frac{ -2 x}{ -1 } + \frac{ 0 x}{ 2 } = 1 $$',
            '$$ \frac{ -2 x}{ -2 } + \frac{ 0 x}{ -2 } = 2 $$',
            '$$ 1 = \frac{x - 1 + 4}{x^2 + x} + \frac{ 3 }{x} $$',
            '$$ -5 x^2 + x + 5 = 4 $$',
            '$$ 1 = \frac{x - 2 + 4}{x^2 + x} + \frac{ -1 }{x} $$'
        ];
        /** @var ProblemFinal[] $found */
        $found = $this->repository->getStudentFiltered(1, 10, 0, ['result' => 0]);
        $this->assertCount(count($expected), $found);
        foreach ($found as $key => $item) {
            $this->assertInstanceOf(ProblemFinal::class, $item);
            $this->assertEquals($expected[$key], (string)$item);
        }

        $expected = [
            '$$ 5 x = 15 - 4 + 2 $$',
            '$$ x - 1 = 4 $$',
        ];
        /** @var ProblemFinal[] $found */
        $found = $this->repository->getStudentFiltered(1, 10, 0, ['result' => 1]);
        $this->assertCount(count($expected), $found);
        foreach ($found as $key => $item) {
            $this->assertInstanceOf(ProblemFinal::class, $item);
            $this->assertEquals($expected[$key], (string)$item);
        }

        $expected = [
            '$$ \frac{ -2 x}{ -1 } + \frac{ 0 x}{ 2 } = 1 $$',
            '$$ \frac{ -2 x}{ -2 } + \frac{ 0 x}{ -2 } = 2 $$',
            '$$ 1 = \frac{x - 1 + 4}{x^2 + x} + \frac{ 3 }{x} $$',
            '$$ -5 x^2 + x + 5 = 4 $$',
            '$$ 1 = \frac{x - 2 + 4}{x^2 + x} + \frac{ -1 }{x} $$',
        ];
        /** @var ProblemFinal[] $found */
        $found = $this->repository->getStudentFiltered(1, 10, 0, ['result' => 2]);
        $this->assertCount(count($expected), $found);
        foreach ($found as $key => $item) {
            $this->assertInstanceOf(ProblemFinal::class, $item);
            $this->assertEquals($expected[$key], (string)$item);
        }

        $expected = [
            '$$ -5 x^2 + x + 5 = 4 $$',
            '$$ 1 = \frac{x - 1 + 4}{x^2 + x} + \frac{ 3 }{x} $$',
            '$$ 1 = \frac{x - 2 + 4}{x^2 + x} + \frac{ -1 }{x} $$',
        ];
        /** @var ProblemFinal[] $found */
        $found = $this->repository->getStudentFiltered(1, 10, 0, [
            'difficulty' => [ '1', '2' ],
            'subTheme' => [ '2' ],
            'result' => '2',
            'sort_by_difficulty' => '1'
        ]);
        $this->assertCount(count($expected), $found);
        foreach ($found as $key => $item) {
            $this->assertInstanceOf(ProblemFinal::class, $item);
            $this->assertEquals($expected[$key], (string)$item);
        }
    }

    public function testApplyFilters(): void
    {
        $qb = $this->repository->getEntityManager()->createQueryBuilder()
            ->select('pf')
            ->addSelect('sc')
            ->from(ProblemFinal::class, 'pf')
            ->innerJoin('pf.subTheme', 'sc')
            ->where('sc.theme = :themeId')
            ->andWhere('pf.studentVisible = true')
            ->setParameter('themeId', 1);

        $qb = $this->repository->applyFilters($qb, [
            'difficulty' => [ '1', '2' ],
            'subTheme' => [ '2' ],
            'result' => '2',
            'sort_by_difficulty' => '1'
        ]);

        $expected = 'SELECT p0_.id AS id_0, p0_.body AS body_1, p0_.text_before AS text_before_2, p0_.text_after AS text_after_3, p0_.success_rate AS success_rate_4, p0_.is_template AS is_template_5, p0_.is_generated AS is_generated_6, p0_.student_visible AS student_visible_7, p0_.created AS created_8, p0_.teacher_level_secured AS teacher_level_secured_9, p1_.matches_index AS matches_index_10, p1_.result AS result_11, s2_.id AS id_12, s2_.created AS created_13, s2_.teacher_level_secured AS teacher_level_secured_14, s2_.label AS label_15, p0_.discr AS discr_16, p0_.problem_type_id AS problem_type_id_17, p0_.difficulty_id AS difficulty_id_18, p0_.sub_theme_id AS sub_theme_id_19, p0_.created_by_id AS created_by_id_20, p1_.problem_template_id AS problem_template_id_21, s2_.theme_id AS theme_id_22, s2_.created_by_id AS created_by_id_23 FROM problem_final p1_ INNER JOIN problem p0_ ON p1_.id = p0_.id INNER JOIN sub_theme s2_ ON p0_.sub_theme_id = s2_.id WHERE s2_.theme_id = ? AND p0_.student_visible = 1 AND p0_.difficulty_id IN (?) AND p0_.sub_theme_id IN (?) AND (p1_.result IS NULL OR p1_.result = \'\') ORDER BY p0_.difficulty_id ASC';
        $this->assertEquals($expected, $qb->getQuery()->getSQL());
    }
}