<?php
declare(strict_types=1);

namespace FizzBuzz;

use PHPUnit\Framework\Attributes\Test;


require_once __DIR__ . '/FizzBuzz.php';

/**
 * x is an int
 * x divisible 3  => "Fizz"
 * x divisible 5 => "Buzz"
 * x divisible 3 and 5 => "FizzBuzz\FizzBuzz"
 * x not divisible 3 or 5 => "x"
 */
#[\PHPUnit\Framework\Attributes\CoversClass(FizzBuzz::class)]
class FizzBuzzTest extends \PHPUnit\Framework\TestCase
{
    #[Test]
    public function it_should_return_the_number_as_string_if_it_is_not_divisible_by_3_or_5()
    {
        $sut = new FizzBuzz();

        $this->assertSame('1', $sut->__invoke(1));
        $this->assertSame('2', $sut->__invoke(2));
    }

    #[Test]
    public function it_should_return_fizz_if_it_is_only_divisible_by_3()
    {
        $sut = new FizzBuzz();

        $this->assertSame('Fizz', $sut->__invoke(3));
        $this->assertSame('Fizz', $sut->__invoke(27));
    }

    #[Test]
    public function it_should_return_buzz_if_it_is_only_divisible_by_5()
    {
        $sut = new FizzBuzz();

        $this->assertSame('Buzz', $sut->__invoke(5));
    }

    #[Test]
    public function it_should_return_fizzbuzz_if_it_is_divisible_by_3_and_5()
    {
        $sut = new FizzBuzz();
        $this->assertSame('FizzBuzz\FizzBuzz', $sut->__invoke(15));
        $this->assertSame('FizzBuzz\FizzBuzz', $sut->__invoke(-15));
    }

}