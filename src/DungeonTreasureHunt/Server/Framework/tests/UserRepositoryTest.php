<?php

namespace DungeonTreasureHunt\Framework\tests;

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
    public function it_should_returns_Empty_Array_If_File_Does_Not_Exist(): void
    {
        unlink($this->tempFile);
        $repository = new UserRepository($this->tempFile);

        $this->assertSame([], $repository->getUsers());
    }

    #[Test]
    public function it_should_Save_And_Retrieve_User(): void
    {
        $repository = new UserRepository($this->tempFile);

        $repository->saveUser('testuser', 'hashed_password');
        $users = $repository->getUsers();

        $this->assertArrayHasKey('testuser', $users);
        $this->assertSame('hashed_password', $users['testuser']);
    }

    #[Test]
    public function it_should_Returns_True_For_Existing_User(): void
    {
        $repository = new UserRepository($this->tempFile);
        $repository->saveUser('testuser', 'hashed_password');

        $this->assertTrue($repository->userExists('testuser'));
    }

    #[Test]
    public function it_should_Returns_False_For_Non_Existing_User(): void
    {
        $repository = new UserRepository($this->tempFile);

        $this->assertFalse($repository->userExists('unknown'));
    }
}