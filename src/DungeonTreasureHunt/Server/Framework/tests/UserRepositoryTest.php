<?php

namespace DungeonTreasureHunt\Framework\tests;

use DungeonTreasureHunt\Backend\services\Password;
use DungeonTreasureHunt\Backend\services\Username;
use DungeonTreasureHunt\Framework\services\UserRepository;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

class UserRepositoryTest extends TestCase
{
    private string $tempFile;

    protected function setUp(): void
    {
        $this->tempFile = tempnam(sys_get_temp_dir(), 'users_');
    }

    protected function tearDown(): void
    {

        if (file_exists($this->tempFile)) {
            unlink($this->tempFile);
        }
    }

    #[Test]
    public function it_should_returns_empty_array_if_file_does_not_exist(): void
    {
        unlink($this->tempFile);
        $repository = new UserRepository($this->tempFile);

        $this->assertSame([], $repository->getUsers());
    }

    #[Test]
    public function it_should_save_and_retrieve_user(): void
    {
        $repository = new UserRepository($this->tempFile);

        $repository->saveUser(new Username('testuser'), new Password('hashed_password'));
        $users = $repository->getUsers();

        $this->assertArrayHasKey('testuser', $users);
        $this->assertSame('hashed_password', $users['testuser']);
    }

    #[Test]
    public function it_should_returns_true_for_existing_user_using_Username(): void
    {
        $repository = new UserRepository($this->tempFile);
        $repository->saveUser(new Username('testuser'), new Password('hashed_password'));

        $this->assertTrue($repository->userExists(new Username('testuser')));
    }

    #[Test]
    public function it_should_returns_false_for_non__using_Username(): void
    {
        $repository = new UserRepository($this->tempFile);

        $this->assertFalse($repository->userExists(new Username('unknown')));
    }


}