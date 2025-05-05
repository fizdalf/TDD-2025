<?php

namespace DungeonTreasureHunt\Backend\tests;

use DungeonTreasureHunt\Backend\services\JWTUserExtractor;
use DungeonTreasureHunt\Backend\services\JwtVerifier;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

class JWTUserExtractorTest extends TestCase
{
    #[Test]
    public function it_should_extract_username_from_valid_token()
    {
        $mockVerifier = $this->createMock(JwtVerifier::class);
        $mockVerifier->method('verify')
            ->willReturn(['username' => 'admin']);

        $sut = new JWTUserExtractor($mockVerifier);

        $username = $sut->extractUsername('dummy-token');

        $this->assertEquals('admin', $username);
    }

    #[Test]
    public function it_should_return_null_if_token_is_invalid()
    {
        $mockVerifier = $this->createMock(JwtVerifier::class);
        $mockVerifier->method('verify')
            ->willReturn(false);

        $sut = new JWTUserExtractor($mockVerifier);

        $username = $sut->extractUsername('invalid-token');

        $this->assertNull($username);
    }

    #[Test]
    public function it_should_return_null_if_token_has_no_username()
    {
        $mockVerifier = $this->createMock(JwtVerifier::class);
        $mockVerifier->method('verify')
            ->willReturn(['no_username' => 'oops']);

        $sut = new JWTUserExtractor($mockVerifier);

        $username = $sut->extractUsername('token-without-username');

        $this->assertNull($username);
    }
}
