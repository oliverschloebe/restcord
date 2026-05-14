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

use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Response;
use PHPUnit\Framework\TestCase;
use RestCord\Logging\MessageFormatter;

/**
 * MessageFormatterTest Class.
 */
class MessageFormatterTest extends TestCase
{
    public function testFormatReplacesTokenInRequestTemplate()
    {
        $token     = 'my-secret-bot-token';
        $formatter = new MessageFormatter('{url} {request}', $token);
        $request   = new Request(
            'GET',
            'https://discord.com/api/v10/users/@me',
            ['Authorization' => 'Bot my-secret-bot-token']
        );

        $formatted = $formatter->format($request);

        $this->assertStringNotContainsString($token, $formatted);
        $this->assertStringContainsString('<TOKEN>', $formatted);
    }

    public function testFormatReplacesTokenInResponseTemplate()
    {
        $token     = 'my-secret-bot-token';
        $formatter = new MessageFormatter('{response}', $token);
        $request   = new Request('GET', 'https://discord.com/api/v10/users/@me');
        $response  = new Response(200, [], 'some body containing my-secret-bot-token in it');

        $formatted = $formatter->format($request, $response);

        $this->assertStringNotContainsString($token, $formatted);
        $this->assertStringContainsString('<TOKEN>', $formatted);
    }

    public function testFormatWithNoTokenOccurrenceIsUnchanged()
    {
        $token     = 'my-secret-bot-token';
        $formatter = new MessageFormatter('{method}', $token);
        $request   = new Request('GET', 'https://discord.com/api/v10/users/@me');

        $formatted = $formatter->format($request);

        $this->assertEquals('GET', $formatted);
    }

    public function testFormatReplacesMultipleTokenOccurrences()
    {
        $token     = 'secret';
        $formatter = new MessageFormatter('{url} {request}', $token);
        $request   = new Request(
            'GET',
            'https://discord.com/api/v10/users/@me',
            [
                'Authorization' => 'Bot secret',
                'X-Custom'      => 'secret',
            ]
        );

        $formatted = $formatter->format($request);

        $this->assertStringNotContainsString($token, $formatted);
    }

    public function testFormatWithErrorTemplate()
    {
        $token     = 'my-secret-bot-token';
        $formatter = new MessageFormatter('{error}', $token);
        $request   = new Request('GET', 'https://discord.com/api/v10/users/@me');
        $error     = new \Exception('my-secret-bot-token in error');

        $formatted = $formatter->format($request, null, $error);

        $this->assertStringNotContainsString($token, $formatted);
        $this->assertStringContainsString('<TOKEN>', $formatted);
    }
}
