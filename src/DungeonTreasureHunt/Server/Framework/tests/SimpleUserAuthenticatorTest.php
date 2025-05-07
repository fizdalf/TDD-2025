<?php

namespace DungeonTreasureHunt\Framework\tests;

use DungeonTreasureHunt\Framework\services\UserRepository;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use DungeonTreasureHunt\Framework\services\SimpleUserAuthenticator;


class SimpleUserAuthenticatorTest extends TestCase
{
    private SimpleUserAuthenticator $authenticator;

    protected function setUp(): void
    {
        $this->testFilePath = sys_get_temp_dir() . '/test_users.txt';

        $hashedPassword = password_hash('1234', PASSWORD_DEFAULT);

        file_put_contents($this->testFilePath, json_encode([
            'admin' => $hashedPassword
        ]));

        $userRepository = new UserRepository($this->testFilePath);
        $this->authenticator = new SimpleUserAuthenticator($userRepository);
    }

    protected function tearDown(): void
    {
        if (file_exists($this->testFilePath)){
            unlink($this->testFilePath);
        }
    }

    #[Test]
    public function it_should_authenticate_valid_admin_credentials()
    {
        $result = $this->authenticator->authenticate('admin', '1234');

        $this->assertTrue($result);
    }

    #[Test]
    public function it_should_reject_invalid_password()
    {
        $result = $this->authenticator->authenticate('admin', 'wrong_password');

        $this->assertFalse($result);
    }

    #[Test]
    public function it_should_reject_invalid_username()
    {
        $result = $this->authenticator->authenticate('nonexistent_user', 'any_password');

        $this->assertFalse($result);
    }

    #[Test]
    public function it_should_be_case_sensitive()
    {
        $result = $this->authenticator->authenticate('ADMIN', '1234');

        $this->assertFalse($result);
    }
}