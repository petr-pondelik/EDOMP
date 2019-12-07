<?php
/**
 * Created by PhpStorm.
 * User: wiedzmin
 * Date: 7.12.19
 * Time: 16:01
 */

namespace App\Tests\CoreModule\Services;


use App\CoreModule\Model\Persistent\Repository\GroupRepository;
use App\CoreModule\Model\Persistent\Repository\SuperGroupRepository;
use App\CoreModule\Model\Persistent\Repository\ThemeRepository;
use App\CoreModule\Services\Authorizator;
use App\Tests\EDOMPTestCase;
use Nette\Security\User;

/**
 * Class AuthorizatorIntegrationTest
 * @package App\Tests\CoreModule\Services
 */
final class AuthorizatorIntegrationTest extends EDOMPTestCase
{
    /**
     * @var Authorizator
     */
    protected $authorizator;

    /**
     * @var User
     */
    protected $user;

    /**
     * @var SuperGroupRepository
     */
    protected $superGroupRepository;

    /**
     * @var GroupRepository
     */
    protected $groupRepository;

    /**
     * @var ThemeRepository
     */
    protected $themeRepository;

    protected function setUp(): void
    {
        parent::setUp();
        $this->authorizator = $this->container->getByType(Authorizator::class);
        $this->user = $this->container->getByType(User::class);
        $this->superGroupRepository = $this->container->getByType(SuperGroupRepository::class);
        $this->groupRepository = $this->container->getByType(GroupRepository::class);
        $this->themeRepository = $this->container->getByType(ThemeRepository::class);
    }

    /**
     * @throws \Nette\Security\AuthenticationException
     */
    public function testIsThemeAllowed(): void
    {
        $this->user->login('srosser5@tuttocitta.it', '12345678');

        $this->assertTrue($this->authorizator->isThemeAllowed($this->user, 1));
        $this->assertTrue($this->authorizator->isThemeAllowed($this->user, 2));
        $this->assertFalse($this->authorizator->isThemeAllowed($this->user, 3));

        $this->user->logout(true);
    }

    /**
     * @throws \Nette\Security\AuthenticationException
     * @throws \Exception
     */
    public function testIsEntityAllowed(): void
    {
        $this->user->login('admin', '12345678');

        $superGroups = $this->superGroupRepository->findAssoc([], 'id');
        $groups = $this->groupRepository->findAssoc([], 'id');
        $themes = $this->themeRepository->findAssoc([], 'id');

        // Assert SuperGroup access for Admin
        $this->assertFalse($this->authorizator->isEntityAllowed($this->user, $superGroups[1]));
        $this->assertFalse($this->authorizator->isEntityAllowed($this->user, $superGroups[2]));
        $this->assertTrue($this->authorizator->isEntityAllowed($this->user, $superGroups[3]));

        // Assert Group access for Admin
        $this->assertFalse($this->authorizator->isEntityAllowed($this->user, $groups[1]));
        $this->assertFalse($this->authorizator->isEntityAllowed($this->user, $groups[2]));
        $this->assertTrue($this->authorizator->isEntityAllowed($this->user, $groups[3]));

        // Assert other entity access for Admin
        $this->assertTrue($this->authorizator->isEntityAllowed($this->user, $themes[1]));

        $this->user->logout(true);

        $this->user->login('jkohneke0@nba.com', '12345678');

        // Assert SuperGroup access for Teacher
        $this->assertFalse($this->authorizator->isEntityAllowed($this->user, $superGroups[1]));
        $this->assertFalse($this->authorizator->isEntityAllowed($this->user, $superGroups[2]));
        $this->assertTrue($this->authorizator->isEntityAllowed($this->user, $superGroups[3]));

        // Assert Group access for Teacher
        $this->assertFalse($this->authorizator->isEntityAllowed($this->user, $groups[1]));
        $this->assertFalse($this->authorizator->isEntityAllowed($this->user, $groups[2]));
        $this->assertTrue($this->authorizator->isEntityAllowed($this->user, $groups[3]));
        $this->assertFalse($this->authorizator->isEntityAllowed($this->user, $groups[6]));

        // Assert other entity access for Teacher
        $this->assertTrue($this->authorizator->isEntityAllowed($this->user, $themes[1]));
        $this->assertFalse($this->authorizator->isEntityAllowed($this->user, $themes[3]));

        $this->user->logout(true);

        $this->user->login('srosser5@tuttocitta.it', '12345678');

        // Assert SuperGroup access for Student
        $this->assertFalse($this->authorizator->isEntityAllowed($this->user, $superGroups[1]));
        $this->assertFalse($this->authorizator->isEntityAllowed($this->user, $superGroups[2]));
        $this->assertFalse($this->authorizator->isEntityAllowed($this->user, $superGroups[3]));

        // Assert Group access for Student
        $this->assertFalse($this->authorizator->isEntityAllowed($this->user, $groups[1]));
        $this->assertFalse($this->authorizator->isEntityAllowed($this->user, $groups[2]));
        $this->assertFalse($this->authorizator->isEntityAllowed($this->user, $groups[3]));
        $this->assertFalse($this->authorizator->isEntityAllowed($this->user, $groups[4]));

        // Assert other entity access for Student
        $this->assertFalse($this->authorizator->isEntityAllowed($this->user, $themes[1]));
        $this->assertFalse($this->authorizator->isEntityAllowed($this->user, $themes[3]));

        $this->user->logout(true);
    }

    protected function tearDown(): void
    {
        parent::tearDown();
        $this->authorizator = null;
        $this->user = null;
        $this->superGroupRepository = null;
        $this->groupRepository = null;
        $this->themeRepository = null;
    }
}