<?php

declare(strict_types=1);

namespace ShlinkioTest\Shlink\Core\Exception;

use Exception;
use LogicException;
use PHPUnit\Framework\TestCase;
use Shlinkio\Shlink\Core\Exception\IpCannotBeLocatedException;
use Shlinkio\Shlink\Core\Exception\RuntimeException;
use Throwable;

class IpCannotBeLocatedExceptionTest extends TestCase
{
    /** @test */
    public function forEmptyAddressInitializesException(): void
    {
        $e = IpCannotBeLocatedException::forEmptyAddress();

        $this->assertTrue($e->isNonLocatableAddress());
        $this->assertEquals('Ignored visit with no IP address', $e->getMessage());
        $this->assertEquals(0, $e->getCode());
        $this->assertNull($e->getPrevious());
    }

    /** @test */
    public function forLocalhostInitializesException(): void
    {
        $e = IpCannotBeLocatedException::forLocalhost();

        $this->assertTrue($e->isNonLocatableAddress());
        $this->assertEquals('Ignored localhost address', $e->getMessage());
        $this->assertEquals(0, $e->getCode());
        $this->assertNull($e->getPrevious());
    }

    /**
     * @test
     * @dataProvider provideErrors
     */
    public function forErrorInitializesException(Throwable $prev): void
    {
        $e = IpCannotBeLocatedException::forError($prev);

        $this->assertFalse($e->isNonLocatableAddress());
        $this->assertEquals('An error occurred while locating IP', $e->getMessage());
        $this->assertEquals($prev->getCode(), $e->getCode());
        $this->assertSame($prev, $e->getPrevious());
    }

    public function provideErrors(): iterable
    {
        yield 'Simple exception with positive code' => [new Exception('Some message', 100)];
        yield 'Runtime exception with negative code' => [new RuntimeException('Something went wrong', -50)];
        yield 'Logic exception with default code' => [new LogicException('Conditions unmet')];
    }
}
