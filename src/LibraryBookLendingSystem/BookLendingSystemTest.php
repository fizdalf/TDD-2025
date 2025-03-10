<?php
declare(strict_types=1);

use PHPUnit\Framework\Attributes\Test;

require_once 'BookLendingSystem.php';

class BookLendingSystemTest extends PHPUnit\Framework\TestCase
{
    #[Test]
    public function it_should_add_a_book()
    {
        $sut = new BookLendingSystem();

        $sut->addBook("someID");
        $this->assertSame("libro Prestado someID", $sut->borrowBook("someID"));
    }

    #[Test]
    public function it_should_throw_book_not_found_exception_when_book_does_not_exist()
    {
        $sut = new BookLendingSystem();


        $this->expectException(BookNotFoundException::class);
        $sut->borrowBook("someID3");
    }

    #[Test]
    public function it_should_throw_book_already_borrowed_when_book_is_already_borrowed()
    {
        $sut = new BookLendingSystem();
        $sut->addBook("someID4");
        $sut->addBook("someID5");

        $sut->borrowBook("someID5");
        $sut->borrowBook("someID4");

        $this->expectException(BookAlreadyBorrowedException::class);
        $sut->borrowBook("someID4");
    }

    #[Test]
    public function it_should_allow_to_return_a_book_when_the_book_is_borrowed()
    {
        $sut = new BookLendingSystem();
        $sut->addBook("someID4");
        $sut->borrowBook("someID4");
        $sut->returnBook("someID4");

        $this->assertSame("libro Prestado someID4", $sut->borrowBook("someID4"));

    }

    #[Test]
    public function it_should_throw_book_not_found_when_returning_a_book_that_does_not_exist(): void
    {

        $sut = new BookLendingSystem();

        $this->expectException(BookNotFoundException::class);

        $sut->returnBook('someID4');

    }
}
