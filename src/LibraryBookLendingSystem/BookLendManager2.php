<?php
declare(strict_types=1);
require_once 'UserLendingSystem.php';
require_once 'BookLendingSystem.php';

class BookLendManager2
{
    private array $bookLendings = [];

    public function lendBookToUser(string $email, string $bookID): string
    {
        $userSystem = new UserLendingSystem();
        $bookSystem = new BookLendingSystem();

        $userSystem->getUser($email);

        if (!isset($bookSystem[$bookID])) {
            throw new BookAlreadyBorrowedException();
        }

        $bookSystem->borrowBook($bookID);
        $this->bookLendings[$bookID] = $email;

        return 'El libro $bookID ha sido prestado a $email';

    }

    public function returnBookFromUser(string $email, string $bookID): string
    {
        $userSystem = new UserLendingSystem();
        $bookSystem = new BookLendingSystem();

        if (!isset($this->bookLendings[$bookID])) {
            throw  new BookNotFoundException();
        }

        if ($this->bookLendings[$bookID] != $email) {
            var_dump("no es el mismo usuario");
        }

        $bookSystem->returnBook($bookID);

        return "El libro $bookID ha sido devuelto por $email";
    }
}