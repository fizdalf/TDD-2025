<?php

namespace DungeonTreasureHunt\Backend\tests;

use DungeonTreasureHunt\Backend\exceptions\InvalidTokenException;
use DungeonTreasureHunt\Backend\http\Request;
use DungeonTreasureHunt\Backend\services\AuthenticatedUserExtractor;
use DungeonTreasureHunt\Backend\services\JWTUserExtractor;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\MockObject\Exception;
use PHPUnit\Framework\TestCase;

class AuthenticatedUserExtractorTest extends TestCase{

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
        $userData = ['username' => 'testuser'];

        $request = new Request(['Authorization' => "Bearer $validToken"]);

        $this->jwtUserExtractor->method('extractUserInfo')
            ->with($validToken)
            ->willReturn($userData);

        $user = $this->authenticatedUserExtractor->extractUser($request);

        $this->assertEquals('testuser', $user['username']);
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

        $this->jwtUserExtractor->method('extractUserInfo')
            ->with('invalid-token')
            ->willReturn(null);

        $this->expectException(InvalidTokenException::class);
        $this->authenticatedUserExtractor->extractUser($request);
    }

    #[Test]
    public function it_should_throw_invalid_token_exception_when_token_does_not_contain_username()
    {
        $request = new Request(['Authorization' => 'Bearer valid-token']);

        $this->jwtUserExtractor->method('extractUserInfo')
            ->with('valid-token')
            ->willReturn(['not-username' => 'value']);

        $this->expectException(InvalidTokenException::class);
        $this->authenticatedUserExtractor->extractUser($request);

    }
}