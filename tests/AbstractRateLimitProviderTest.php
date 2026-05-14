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

use GuzzleHttp\Psr7\Request;
use PHPUnit\Framework\TestCase;
use RestCord\RateLimit\Provider\MemoryRateLimitProvider;

/**
 * AbstractRateLimitProviderTest Class
 *
 * Tests getRoute() and stripMinorParameters() via MemoryRateLimitProvider,
 * which inherits these methods from AbstractRateLimitProvider without override.
 */
class AbstractRateLimitProviderTest extends TestCase
{
    private MemoryRateLimitProvider $provider;

    protected function setUp(): void
    {
        $this->provider = new MemoryRateLimitProvider();
    }

    public function testGetRouteReturnsUriForGenericRequest()
    {
        $request = new Request('GET', 'https://discord.com/api/v10/gateway');
        $route   = $this->provider->getRoute($request);
        $this->assertEquals('https://discord.com/api/v10/gateway', $route);
    }

    public function testGetRouteAddsPrefixForDeleteMessageRequest()
    {
        $request = new Request('DELETE', 'https://discord.com/api/v10/channels/123/messages/456');
        $route   = $this->provider->getRoute($request);
        $this->assertStringStartsWith('DELETE-', $route);
    }

    public function testGetRouteDoesNotAddPrefixForGetMessageRequest()
    {
        $request = new Request('GET', 'https://discord.com/api/v10/channels/123/messages/456');
        $route   = $this->provider->getRoute($request);
        $this->assertStringNotContainsString('DELETE-', $route);
    }

    public function testGetRouteDoesNotAddPrefixForDeleteWithoutMessages()
    {
        $request = new Request('DELETE', 'https://discord.com/api/v10/channels/123/permissions/456');
        $route   = $this->provider->getRoute($request);
        $this->assertStringNotContainsString('DELETE-', $route);
    }

    public function testStripMinorParametersForChannelRoute()
    {
        // Minor parameter (message ID) should be stripped after the channel ID
        $request = new Request('GET', 'https://discord.com/api/v10/channels/123456789/messages/987654321');
        $route   = $this->provider->getRoute($request);

        // Channel ID (major parameter) must be preserved
        $this->assertStringContainsString('123456789', $route);
        // Message ID (minor parameter) must not appear as a standalone number
        $this->assertStringNotContainsString('987654321', $route);
    }

    public function testStripMinorParametersForGuildRoute()
    {
        $request = new Request('GET', 'https://discord.com/api/v10/guilds/111222333/members/444555666');
        $route   = $this->provider->getRoute($request);

        $this->assertStringContainsString('111222333', $route);
        $this->assertStringNotContainsString('444555666', $route);
    }

    public function testStripMinorParametersForWebhookRoute()
    {
        $request = new Request('POST', 'https://discord.com/api/v10/webhooks/789012345/some-token/messages/111');
        $route   = $this->provider->getRoute($request);

        $this->assertStringContainsString('789012345', $route);
    }

    public function testStripMinorParametersForUserGuildsRoute()
    {
        $request = new Request('DELETE', 'https://discord.com/api/v10/users/@me/guilds/999888777');
        $route   = $this->provider->getRoute($request);

        $this->assertStringContainsString('999888777', $route);
    }

    public function testGetRequestTimeReturnsFloat()
    {
        $request = new Request('GET', 'https://discord.com/api/v10/channels/123');
        $before  = microtime(true);
        $time    = $this->provider->getRequestTime($request);
        $after   = microtime(true);

        $this->assertIsFloat($time);
        $this->assertGreaterThanOrEqual($before, $time);
        $this->assertLessThanOrEqual($after, $time);
    }

    public function testNonMatchingRouteIsReturnedUnchanged()
    {
        // A URL that matches none of the Discord API patterns
        $request = new Request('GET', 'https://example.com/foo/123/bar/456');
        $route   = $this->provider->getRoute($request);
        $this->assertEquals('https://example.com/foo/123/bar/456', $route);
    }
}
