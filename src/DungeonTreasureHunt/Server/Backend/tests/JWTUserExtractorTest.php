<?php

namespace DungeonTreasureHunt\Backend\tests;

use DungeonTreasureHunt\Backend\services\JWTUserExtractor;
use DungeonTreasureHunt\Backend\services\JwtHandler;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

require_once __DIR__ . '/../services/JWTUserExtractor.php';
require_once __DIR__ . '/../services/JwtHandler.php';

class JWTUserExtractorTest extends TestCase
{
    #[Test]
    public function it_should_extract_username_from_valid_token()
    {
        $token = JwtHandler::generateToken(["username" => "admin"]);

        $sut = new JWTUserExtractor(new JwtHandler());

        $username = $sut->extractUsername($token);

        $this->assertEquals("admin", $username);
    }

    #[Test]
    public function it_should_return_null_if_token_is_invalid()
    {
        $token = "goofy token";

        $sut = new JWTUserExtractor(new JwtHandler());

        $username = $sut->extractUsername($token);

        $this->assertNull($username);
    }

    #[Test]
    public function it_should_return_null_if_token_has_no_username()
    {
        $token = JwtHandler::generateToken(["no_username" => "oops"]);

        $extractor = new JWTUserExtractor(new JwtHandler());

        $username = $extractor->extractUsername($token);

        $this->assertNull($username);
    }

}