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
use RestCord\RateLimit\Provider\MemoryRateLimitProvider;

/**
 * MemoryRateLimitProviderTest Class.
 */
class MemoryRateLimitProviderTest extends TestCase
{
    private MemoryRateLimitProvider $provider;

    protected function setUp(): void
    {
        $this->provider = new MemoryRateLimitProvider();
    }

    public function testGetLastRequestTimeReturnsNullForUnknownRoute()
    {
        $request = new Request('GET', 'https://discord.com/api/v10/channels/123');
        $this->assertNull($this->provider->getLastRequestTime($request));
    }

    public function testSetAndGetLastRequestTime()
    {
        $request = new Request('GET', 'https://discord.com/api/v10/channels/123');

        $before = microtime(true);
        $this->provider->setLastRequestTime($request);
        $after = microtime(true);

        $time = $this->provider->getLastRequestTime($request);

        $this->assertIsFloat($time);
        $this->assertGreaterThanOrEqual($before, $time);
        $this->assertLessThanOrEqual($after, $time);
    }

    public function testGetRequestAllowanceReturnsZeroForUnknownRoute()
    {
        $request = new Request('GET', 'https://discord.com/api/v10/channels/123');
        $this->assertEquals(0, $this->provider->getRequestAllowance($request));
    }

    public function testGetRequestAllowanceReturnsZeroWhenNoResetSet()
    {
        $request = new Request('GET', 'https://discord.com/api/v10/channels/123');
        $this->provider->setLastRequestTime($request);
        $this->assertEquals(0, $this->provider->getRequestAllowance($request));
    }

    public function testSetRequestAllowanceWithRateLimitExhausted()
    {
        $request   = new Request('GET', 'https://discord.com/api/v10/channels/123');
        $resetTime = time() + 60; // 60 seconds in the future

        $response = new Response(429, [
            'X-RateLimit-Remaining' => ['0'],
            'X-RateLimit-Reset'     => [(string) $resetTime],
        ]);

        $this->provider->setRequestAllowance($request, $response);

        $allowance = $this->provider->getRequestAllowance($request);
        $this->assertGreaterThan(0, $allowance);
    }

    public function testSetRequestAllowanceSkipsWhenRemainingIsPositive()
    {
        $request  = new Request('GET', 'https://discord.com/api/v10/channels/123');
        $response = new Response(200, [
            'X-RateLimit-Remaining' => ['5'],
            'X-RateLimit-Reset'     => [(string) (time() + 60)],
        ]);

        $this->provider->setRequestAllowance($request, $response);

        $this->assertEquals(0, $this->provider->getRequestAllowance($request));
    }

    public function testSetRequestAllowanceSkipsWhenNoHeaders()
    {
        $request  = new Request('GET', 'https://discord.com/api/v10/channels/123');
        $response = new Response(200);

        $this->provider->setRequestAllowance($request, $response);

        $this->assertEquals(0, $this->provider->getRequestAllowance($request));
    }

    public function testSetRequestAllowanceSkipsWhenMissingRemainingHeader()
    {
        $request  = new Request('GET', 'https://discord.com/api/v10/channels/123');
        $response = new Response(200, [
            'X-RateLimit-Reset' => [(string) (time() + 60)],
        ]);

        $this->provider->setRequestAllowance($request, $response);

        $this->assertEquals(0, $this->provider->getRequestAllowance($request));
    }

    public function testSetRequestAllowanceSkipsWhenMissingResetHeader()
    {
        $request  = new Request('GET', 'https://discord.com/api/v10/channels/123');
        $response = new Response(200, [
            'X-RateLimit-Remaining' => ['0'],
        ]);

        $this->provider->setRequestAllowance($request, $response);

        $this->assertEquals(0, $this->provider->getRequestAllowance($request));
    }

    public function testDifferentRoutesAreTrackedIndependently()
    {
        $request1 = new Request('GET', 'https://discord.com/api/v10/channels/111');
        $request2 = new Request('GET', 'https://discord.com/api/v10/channels/222');

        $this->provider->setLastRequestTime($request1);

        $this->assertIsFloat($this->provider->getLastRequestTime($request1));
        $this->assertNull($this->provider->getLastRequestTime($request2));
    }
}
