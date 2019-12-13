<?php
/**
 * Created by PhpStorm.
 * User: wiedzmin
 * Date: 7.12.19
 * Time: 15:26
 */

namespace App\Tests\CoreModule\Services;

use App\CoreModule\Model\Persistent\Repository\ThemeRepository;
use App\CoreModule\Services\Authenticator;
use App\Tests\EDOMPUnitTestCase;
use Nette\Security\AuthenticationException;
use Nette\Security\Identity;

/**
 * Class AuthenticatorUnitTest
 * @package App\Tests\CoreModule\Services
 */
final class AuthenticatorIntegrationTest extends EDOMPUnitTestCase
{
    /**
     * @var Authenticator
     */
    protected $authenticator;

    /**
     * @var ThemeRepository
     */
    protected $themeRepository;

    /**
     * @throws \Exception
     */
    protected function setUp(): void
    {
        parent::setUp();
        $this->authenticator = $this->container->getByType(Authenticator::class);
        $this->themeRepository = $this->container->getByType(ThemeRepository::class);
    }

    /**
     * @throws AuthenticationException
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function testAuthenticate(): void
    {
        // Prepare expected Identity object for Admin User
        $identityExpected = new Identity(1, 'admin', [
            'username' => 'admin',
            'themes' => [],
            'roleLabel' => 'Administrátor',
            'firstName' => 'Petr',
            'lastName' => 'Pondělík'
        ]);

        // Authenticate admin User and get it's identity
        $identity = $this->authenticator->authenticate([
            'admin',
            '12345678',
            true
        ]);

        // Test created Identity against expected Identity
        $this->assertEquals($identityExpected, $identity);

        // Prepare expected Identity object for Teacher User
        $identityExpected = new Identity(2, 'teacher', [
            'username' => 'jkohneke0@nba.com',
            'themes' => [],
            'roleLabel' => 'Učitel',
            'firstName' => 'Joyce',
            'lastName' => 'Kohneke'
        ]);

        // Authenticate admin User and get it's identity
        $identity = $this->authenticator->authenticate([
            'jkohneke0@nba.com',
            '12345678',
            true
        ]);

        // Test created Identity against expected Identity
        $this->assertEquals($identityExpected, $identity);

        // Prepare expected Identity object for Student User
        $identityExpected = new Identity(6, 'student', [
            'username' => 'srosser5@tuttocitta.it',
            'themes' => [
                2 => '2. Posloupnosti'
            ],
            'roleLabel' => 'Student',
            'firstName' => 'Sara',
            'lastName' => 'Rosser'
        ]);

        // Authenticate student User and get it's Identity
        $identity = $this->authenticator->authenticate([
            'srosser5@tuttocitta.it',
            '12345678',
            false
        ]);

        // Test created Identity against expected Identity
        $this->assertEquals($identityExpected, $identity);
    }

    /**
     * @throws AuthenticationException
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function testAuthenticateAllIncorrect(): void
    {
        $this->expectException(AuthenticationException::class);

        // Authenticate with invalid credentials
        $this->authenticator->authenticate([
            'TEST_USERNAME_FIRST',
            'TEST_PASSWORD_FIRST',
            false
        ]);
    }

    /**
     * @throws AuthenticationException
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function testAuthenticateUsernameIncorrect(): void
    {
        $this->expectException(AuthenticationException::class);

        // Authenticate with invalid credentials
        $this->authenticator->authenticate([
            'TEST_USERNAME_FIRST',
            '12345678',
            false
        ]);
    }

    /**
     * @throws AuthenticationException
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function testAuthenticatePasswordIncorrect(): void
    {
        $this->expectException(AuthenticationException::class);

        // Authenticate with invalid credentials
        $this->authenticator->authenticate([
            'admin',
            'TEST_PASSWORD_FIRST',
            false
        ]);
    }
}