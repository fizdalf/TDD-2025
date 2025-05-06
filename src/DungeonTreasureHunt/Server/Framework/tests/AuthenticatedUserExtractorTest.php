<?php

namespace DungeonTreasureHunt\Framework\tests;

use DungeonTreasureHunt\Backend\exceptions\InvalidTokenException;
use DungeonTreasureHunt\Framework\models\AuthenticatedUser;
use DungeonTreasureHunt\Framework\services\AuthenticatedUserExtractor;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\MockObject\Exception;
use PHPUnit\Framework\TestCase;
use DungeonTreasureHunt\Framework\http\Request;
use DungeonTreasureHunt\Framework\services\JWTUserExtractor;

class AuthenticatedUserExtractorTest extends TestCase
{

    private JWTUserExtractor $jwtUserExtractor;
    private AuthenticatedUserExtractor $authenticatedUserExtractor;

    /**
     * @throws Exception
     */
    protected function setUp(): void
    {
        $this->jwtUserExtractor = $this->createMock(JWTUserExtractor::class);
        $this->authenticatedUserExtractor = new AuthenticatedUserExtractor($this->jwtUserExtractor);
    }

    /**
     * @throws InvalidTokenException
     * @throws Exception
     */
    #[Test]
    public function it_should_extract_user_when_valid_token_is_provided()
    {
        $validToken = 'valid-token';


        $request = new Request(['Authorization' => "Bearer $validToken"]);

        $this->jwtUserExtractor
            ->expects($this->once())
            ->method('userFromToken')
            ->with($validToken)
            ->willReturn(new AuthenticatedUser('testuser'));

        $user = $this->authenticatedUserExtractor->extractUser($request);

        $this->assertEquals('testuser', $user->name);
    }

    #[Test]
    public function it_should_throw_invalid_token_exception_when_no_authorization_header_is_present()
    {
        $request = new Request([]);

        $this->expectException(InvalidTokenException::class);
        $this->authenticatedUserExtractor->extractUser($request);
    }

    #[Test]
    public function it_should_throw_invalid_token_exception_when_token_is_invalid()
    {
        $request = new Request(['Authorization' => 'Bearer invalid-token']);
        $this->expectException(InvalidTokenException::class);
        $this->authenticatedUserExtractor->extractUser($request);
    }
}