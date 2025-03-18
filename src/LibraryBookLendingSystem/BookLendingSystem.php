<?php
declare(strict_types=1);

require_once 'BookNotFoundException.php';
require_once 'BookAlreadyBorrowedException.php';
require_once 'BookStatus.php';

class BookLendingSystem
{
    private array $bookStatuses = [];

    public function addBook(string $id): void
    {
        $this->bookStatuses[$id] = BookStatus::Available;
    }

    /**
     * @throws BookNotFoundException
     * @throws BookAlreadyBorrowedException
     */
    public function borrowBook(string $id): string
    {
        $this->confirmBookExists($id);
        $isBetterBorrowed = $this->bookStatuses[$id];

        $this->confirmBookIsAvailableBetter($isBetterBorrowed);

        $this->bookStatuses[$id] = BookStatus::Borrowed;



        return 'libro Prestado ' . $id;
    }

    /**
     * @throws BookNotFoundException
     */
    public function returnBook(string $id): void
    {
        $this->confirmBookExists($id);
        $this->bookStatuses[$id] = BookStatus::Available;
    }

    /**
     * @throws BookNotFoundException
     */
    private function confirmBookExists(string $id): void
    {
        if (!isset($this->bookStatuses[$id])) {
            throw new BookNotFoundException();
        }
    }

    /**
     * @throws BookAlreadyBorrowedException
     */
    private function confirmBookIsAvailableBetter(BookStatus $isBookBorrowed): void
    {
        if ($isBookBorrowed === BookStatus::Borrowed) {
            throw new BookAlreadyBorrowedException();
        }
    }




}