<?php
declare(strict_types=1);

use PHPUnit\Framework\Attributes\Test;

require_once 'UserNotExistsException.php';
require_once 'UserLendingSystem.php';

class UserLendingSystemTest extends PHPUnit\Framework\TestCase
{

    #[Test]
    public function it_should_add_a_user()
    {
        $sut = new UserLendingSystem();

        $sut->addUser("Samuel", "Riveira", "samuelriveira@gmail.com");
        $this->assertSame("SamuelRiveirasamuelriveira@gmail.com", $sut->getUser("samuelriveira@gmail.com"));
    }

    #[Test]
    public function it_should_throw_when_user_does_not_exists()
    {
        $sut = new UserLendingSystem();

        $this->expectException(UserNotExistsException::class);
        $sut->getUser("correoRandom@gmail.com");
    }

}