<?php

/*
 * Copyright 2026 Oliver Schlöbe
 *
 * This source file is subject to the license that is bundled
 * with this source code in the file LICENSE
 *
 * @package     restcord/restcord
 * @copyright   Aaron Scherer 2017
 * @license     MIT
 */

namespace RestCord\Tests;

use GuzzleHttp\Command\Guzzle\GuzzleClient;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\Psr7\Response;
use PHPUnit\Framework\TestCase;
use RestCord\DiscordClient;
use Symfony\Component\OptionsResolver\Exception\InvalidOptionsException;

/**
 * DiscordClientTest Class.
 */
class DiscordClientTest extends TestCase
{
    public function testClientCreatesWithDefaultVersion()
    {
        $client = new DiscordClient(['token' => 'fake-token']);
        $this->assertInstanceOf(DiscordClient::class, $client);
    }

    public function testClientCreatesWithVersion9()
    {
        $client = new DiscordClient(['token' => 'fake-token', 'version' => 9]);
        $this->assertInstanceOf(DiscordClient::class, $client);
    }

    public function testClientCreatesWithVersion10()
    {
        $client = new DiscordClient(['token' => 'fake-token', 'version' => 10]);
        $this->assertInstanceOf(DiscordClient::class, $client);
    }

    public function testClientAccessesChannelCategory()
    {
        $client = new DiscordClient(['token' => 'fake-token']);
        $this->assertInstanceOf(GuzzleClient::class, $client->channel);
    }

    public function testClientAccessesGuildCategory()
    {
        $client = new DiscordClient(['token' => 'fake-token']);
        $this->assertInstanceOf(GuzzleClient::class, $client->guild);
    }

    public function testClientAccessesUserCategory()
    {
        $client = new DiscordClient(['token' => 'fake-token']);
        $this->assertInstanceOf(GuzzleClient::class, $client->user);
    }

    public function testClientAccessesWebhookCategory()
    {
        $client = new DiscordClient(['token' => 'fake-token']);
        $this->assertInstanceOf(GuzzleClient::class, $client->webhook);
    }

    public function testClientAccessesInviteCategory()
    {
        $client = new DiscordClient(['token' => 'fake-token']);
        $this->assertInstanceOf(GuzzleClient::class, $client->invite);
    }

    public function testClientAccessesVoiceCategory()
    {
        $client = new DiscordClient(['token' => 'fake-token']);
        $this->assertInstanceOf(GuzzleClient::class, $client->voice);
    }

    public function testClientThrowsOnInvalidCategory()
    {
        $client = new DiscordClient(['token' => 'fake-token']);
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('No category with the name: nonexistent');
        $client->nonexistent;
    }

    public function testClientThrowsOnInvalidCategoryName()
    {
        $client = new DiscordClient(['token' => 'fake-token']);
        $this->expectException(\Exception::class);
        $client->foo;
    }

    public function testClientWithBotTokenType()
    {
        $client = new DiscordClient(['token' => 'fake-token', 'tokenType' => 'Bot']);
        $this->assertInstanceOf(DiscordClient::class, $client);
    }

    public function testClientWithOAuthTokenType()
    {
        $client = new DiscordClient(['token' => 'fake-token', 'tokenType' => 'OAuth']);
        $this->assertInstanceOf(DiscordClient::class, $client);
    }

    public function testClientThrowsOnInvalidTokenType()
    {
        $this->expectException(InvalidOptionsException::class);
        new DiscordClient(['token' => 'fake-token', 'tokenType' => 'Invalid']);
    }

    public function testClientThrowsOnNonStringToken()
    {
        $this->expectException(InvalidOptionsException::class);
        new DiscordClient(['token' => 12345]);
    }

    public function testClientWithThrowOnRatelimitEnabled()
    {
        $client = new DiscordClient(['token' => 'fake-token', 'throwOnRatelimit' => true]);
        $this->assertInstanceOf(DiscordClient::class, $client);
    }

    public function testClientWithCustomGuzzleOptions()
    {
        $mock   = new MockHandler([new Response(200, [], '{}')]);
        $client = new DiscordClient([
            'token'         => 'fake-token',
            'guzzleOptions' => ['handler' => $mock],
        ]);
        $this->assertInstanceOf(DiscordClient::class, $client);
    }

    public function testClientWithMiddleware()
    {
        $middlewareCalled = false;
        $middleware       = function (callable $handler) use (&$middlewareCalled) {
            return function ($request, $options) use ($handler, &$middlewareCalled) {
                $middlewareCalled = true;

                return $handler($request, $options);
            };
        };

        $client = new DiscordClient([
            'token'      => 'fake-token',
            'middleware' => [$middleware],
        ]);
        $this->assertInstanceOf(DiscordClient::class, $client);
    }

    public function testGetChannelReturnsCorrectOperations()
    {
        $client  = new DiscordClient(['token' => 'fake-token']);
        $channel = $client->channel;
        $this->assertInstanceOf(GuzzleClient::class, $channel);
        $this->assertNotNull($channel->getDescription()->getOperation('getChannel'));
    }

    public function testGetGuildReturnsCorrectOperations()
    {
        $client = new DiscordClient(['token' => 'fake-token']);
        $guild  = $client->guild;
        $this->assertInstanceOf(GuzzleClient::class, $guild);
        $this->assertNotNull($guild->getDescription()->getOperation('getGuild'));
    }
}
