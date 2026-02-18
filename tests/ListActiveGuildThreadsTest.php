<?php

/*
 * Copyright 2017 Aaron Scherer
 *
 * This source file is subject to the license that is bundled
 * with this source code in the file LICENSE
 *
 * @package     restcord/restcord
 * @copyright   Aaron Scherer 2017
 * @license     MIT
 */

namespace RestCord\Tests;

use GuzzleHttp\Command\Result;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\Psr7\Response;
use PHPUnit\Framework\TestCase;
use RestCord\DiscordClient;
use RestCord\Model\Channel\Channel;
use RestCord\Model\Guild\GuildMember;

class ListActiveGuildThreadsTest extends TestCase
{
    public function testListActiveGuildThreadsReturnsTypedResult()
    {
        $responseBody = json_encode([
            'threads' => [
                ['id' => 123, 'type' => 11, 'name' => 'test-thread', 'guild_id' => 456],
                ['id' => 789, 'type' => 11, 'name' => 'another-thread', 'guild_id' => 456],
            ],
            'members' => [
                ['deaf' => false, 'mute' => false, 'nick' => 'user1', 'roles' => [1, 2]],
                ['deaf' => true, 'mute' => true, 'nick' => 'user2', 'roles' => [3]],
            ],
        ]);

        $mock = new MockHandler([
            new Response(200, ['Content-Type' => 'application/json'], $responseBody),
        ]);

        $client = new DiscordClient([
            'token' => 'fake-token',
            'guzzleOptions' => [
                'handler' => $mock,
            ],
        ]);

        $result = $client->guild->listActiveGuildThreads(['guild.id' => 456]);

        $this->assertInstanceOf(Result::class, $result);

        // Verify threads are Channel instances
        $this->assertCount(2, $result['threads']);
        $this->assertInstanceOf(Channel::class, $result['threads'][0]);
        $this->assertInstanceOf(Channel::class, $result['threads'][1]);
        $this->assertEquals(123, $result['threads'][0]->id);
        $this->assertEquals('test-thread', $result['threads'][0]->name);
        $this->assertEquals(11, $result['threads'][0]->type);
        $this->assertEquals(789, $result['threads'][1]->id);
        $this->assertEquals('another-thread', $result['threads'][1]->name);

        // Verify members are GuildMember instances
        $this->assertCount(2, $result['members']);
        $this->assertInstanceOf(GuildMember::class, $result['members'][0]);
        $this->assertInstanceOf(GuildMember::class, $result['members'][1]);
        $this->assertEquals(false, $result['members'][0]->deaf);
        $this->assertEquals('user1', $result['members'][0]->nick);
        $this->assertEquals(true, $result['members'][1]->deaf);
        $this->assertEquals(true, $result['members'][1]->mute);
    }

    public function testListActiveGuildThreadsHandlesEmptyResponse()
    {
        $responseBody = json_encode([
            'threads' => [],
            'members' => [],
        ]);

        $mock = new MockHandler([
            new Response(200, ['Content-Type' => 'application/json'], $responseBody),
        ]);

        $client = new DiscordClient([
            'token' => 'fake-token',
            'guzzleOptions' => [
                'handler' => $mock,
            ],
        ]);

        $result = $client->guild->listActiveGuildThreads(['guild.id' => 456]);

        $this->assertInstanceOf(Result::class, $result);
        $this->assertCount(0, $result['threads']);
        $this->assertCount(0, $result['members']);
    }
}
