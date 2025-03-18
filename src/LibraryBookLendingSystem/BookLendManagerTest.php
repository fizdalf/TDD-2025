<?php
declare(strict_types=1);

require_once 'BookLendManager.php';
require_once 'UserNotExistsException.php';
require_once 'UserLendingSystem.php';
require_once 'BookLendingSystem.php';

use PHPUnit\Framework\Attributes\Test;

class BookLendManagerTest extends PHPUnit\Framework\TestCase
{

    #[Test]
    public function it_should_allow_a_user_to_borrow_a_book_that_is_available()
    {
        $userLendingSystem = new UserLendingSystem();
        $userLendingSystem->addUser("Samuel", "Riveira", "samuelriveira@gmail.com");

        $bookLendingSystem = new BookLendingSystem();
        $bookLendingSystem->addBook('SomeID');

        $sut = new BookLendManager($userLendingSystem, $bookLendingSystem);
        $sut->borrow("someID","samuelriveira@gmail.com");

        $this->expectException(BookAlreadyBorrowedException::class);

        $bookLendingSystem->borrowBook('someID');
    }
}