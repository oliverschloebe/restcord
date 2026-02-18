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
use RestCord\Model\Thread\Thread;
use RestCord\Model\Thread\Member;

class ListActiveGuildThreadsTest extends TestCase
{
    public function testListActiveGuildThreadsReturnsTypedResult()
    {
        $responseBody = json_encode([
            'threads' => [
                [
                    'id' => 1468665862922965146,
                    'type' => 11,
                    'last_message_id' => 1473011515559907431,
                    'flags' => 0,
                    'guild_id' => 1274992373612679219,
                    'name' => 'Sweet/Vicious',
                    'parent_id' => 1412704496017997874,
                    'rate_limit_per_user' => 0,
                    'bitrate' => 64000,
                    'user_limit' => 0,
                    'rtc_region' => null,
                    'owner_id' => 960862862618800129,
                    'thread_metadata' => [
                        'archived' => false,
                        'archive_timestamp' => '2026-02-04T17:53:48.213000+00:00',
                        'auto_archive_duration' => 10080,
                        'locked' => false,
                        'create_timestamp' => '2026-02-04T17:53:48.213000+00:00',
                    ],
                    'message_count' => 347,
                    'member_count' => 7,
                    'total_message_sent' => 348,
                ],
                [
                    'id' => 1463445161668247656,
                    'type' => 11,
                    'name' => 'A Night of the Seven Kingdoms',
                ],
            ],
            'members' => [
                [
                    'id' => 1279037614053265430,
                    'user_id' => 1277616012908302356,
                    'join_timestamp' => '2025-05-01T13:55:03.174000+00:00',
                    'flags' => 1,
                    'muted' => null,
                    'mute_config' => null,
                ],
                [
                    'id' => 1275043236632727643,
                    'user_id' => 1277616012908302356,
                    'join_timestamp' => '2026-02-17T16:15:13.911000+00:00',
                    'flags' => 1,
                    'muted' => null,
                    'mute_config' => null,
                ],
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

        // Verify threads are Thread instances
        $this->assertCount(2, $result['threads']);
        $this->assertInstanceOf(Thread::class, $result['threads'][0]);
        $this->assertInstanceOf(Thread::class, $result['threads'][1]);
        $this->assertEquals(1468665862922965146, $result['threads'][0]->id);
        $this->assertEquals('Sweet/Vicious', $result['threads'][0]->name);
        $this->assertEquals(11, $result['threads'][0]->type);
        $this->assertEquals(0, $result['threads'][0]->flags);
        $this->assertEquals(347, $result['threads'][0]->message_count);
        $this->assertEquals(7, $result['threads'][0]->member_count);
        $this->assertEquals(348, $result['threads'][0]->total_message_sent);
        $this->assertEquals(64000, $result['threads'][0]->bitrate);
        $this->assertIsArray($result['threads'][0]->thread_metadata);
        $this->assertEquals(1463445161668247656, $result['threads'][1]->id);
        $this->assertEquals('A Night of the Seven Kingdoms', $result['threads'][1]->name);

        // Verify members are Thread\Member instances
        $this->assertCount(2, $result['members']);
        $this->assertInstanceOf(Member::class, $result['members'][0]);
        $this->assertInstanceOf(Member::class, $result['members'][1]);
        $this->assertEquals(1279037614053265430, $result['members'][0]->id);
        $this->assertEquals(1, $result['members'][0]->flags);
        $this->assertNull($result['members'][0]->muted);
        $this->assertNull($result['members'][0]->mute_config);
        $this->assertEquals(1275043236632727643, $result['members'][1]->id);
        $this->assertEquals(1, $result['members'][1]->flags);
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
