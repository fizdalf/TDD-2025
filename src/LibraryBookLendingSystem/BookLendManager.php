<?php
declare(strict_types=1);
require_once 'UserLendingSystem.php';
require_once 'BookLendingSystem.php';
require_once 'BookAndEmailNotExistsException.php';

class BookLendManager
{

    private array $LendInformationBookEmail = [];


    public function __construct(
        private UserLendingSystem $userSystem,
        private BookLendingSystem $bookSystem,
    )
    {
    }

    public function borrow(string $bookID, string $email){
        $this->userSystem->getUser($email);

        if (!isset($this->bookSystem[$bookID])) {
            throw new BookAlreadyBorrowedException();
        }

        $this->bookSystem->borrowBook($bookID);

        $this->LendInformationBookEmail[$bookID] = $email;
    }
}