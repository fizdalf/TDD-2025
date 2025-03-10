<?php
declare(strict_types=1);

namespace FizzBuzz;
class FizzBuzz
{
    public function __invoke(int $x): string
    {
        if ($this->isDivisibleBy($x, 5) && $this->isDivisibleBy($x, 3)) {
            return 'FizzBuzz\FizzBuzz';
        }

        if ($this->isDivisibleBy($x, 5)) {
            return 'Buzz';
        }
        if ($this->isDivisibleBy($x, 3)) {
            return 'Fizz';
        }
        return (string)$x;
    }

    /**
     * @param int $x
     * @param int $divisor
     * @return bool
     */
    private function isDivisibleBy(int $x, int $divisor): bool
    {
        return $x % $divisor === 0;
    }
}