<?php

namespace DungeonTreasureHunt\Backend\tests;

use DungeonTreasureHunt\Backend\models\AuthenticatedUser;
use DungeonTreasureHunt\Backend\services\JWTUserExtractor;
use DungeonTreasureHunt\Backend\services\JwtVerifier;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

class JWTUserExtractorTest extends TestCase
{
    private JwtVerifier $jwtVerifier;

    protected function setUp(): void
    {
        $this->jwtVerifier = $this->createMock(JwtVerifier::class);
    }

    #[Test]
    public function it_should_return_user_when_token_is_valid(): void
    {
        $payload = ['username' => 'admin'];

        $this->jwtVerifier
            ->method('verify')
            ->willReturn($payload);

        $extractor = new JWTUserExtractor($this->jwtVerifier);
        $result = $extractor->userFromToken('valid_token');

        $expectedUser = AuthenticatedUser::fromRaw($payload);

        $this->assertEquals($expectedUser, $result);
    }

    #[Test]
    public function it_should_return_null_when_token_is_invalid(): void
    {
        $this->jwtVerifier
            ->method('verify')
            ->willReturn(false);

        $extractor = new JWTUserExtractor($this->jwtVerifier);
        $result = $extractor->userFromToken('invalid_token');

        $this->assertNull($result);
    }

    #[Test]
    public function it_should_return_null_when_verify_returns_null(): void
    {
        $this->jwtVerifier
            ->method('verify')
            ->willReturn(null);

        $extractor = new JWTUserExtractor($this->jwtVerifier);
        $result = $extractor->userFromToken('null_token');

        $this->assertNull($result);
    }


}
