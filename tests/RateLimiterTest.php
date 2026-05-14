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
use GuzzleHttp\Psr7\Response;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Log\LogLevel;
use RestCord\RateLimit\Provider\AbstractRateLimitProvider;
use RestCord\RateLimit\RateLimiter;
use RestCord\RateLimit\RatelimitException;

/**
 * Concrete provider used only in these tests to control delay values.
 *
 * When $allowance > 0, getLastRequestTime() returns the current time so that
 * delay = allowance - (requestTime - lastRequestTime) ≈ allowance > 0.
 * When $allowance = 0, getLastRequestTime() returns null so delay stays at 0.
 */
class FixedAllowanceProvider extends AbstractRateLimitProvider
{
    private float $allowance;

    public function __construct(float $allowance = 0.0)
    {
        $this->allowance = $allowance;
    }

    public function getLastRequestTime(RequestInterface $request): ?float
    {
        // Return current time so (requestTime - lastRequestTime) ≈ 0,
        // making delay ≈ allowance.
        return $this->allowance > 0.0 ? microtime(true) : null;
    }

    public function setLastRequestTime(RequestInterface $request): void
    {
    }

    public function getRequestAllowance(RequestInterface $request): float
    {
        return $this->allowance;
    }

    public function setRequestAllowance(RequestInterface $request, ResponseInterface $response): void
    {
    }
}

/**
 * Subclass exposing protected methods for testing.
 */
class TestableRateLimiter extends RateLimiter
{
    public function publicGetLogLevel(RequestInterface $request): string
    {
        return $this->getLogLevel($request);
    }

    public function publicGetDefaultLogLevel(): string
    {
        return $this->getDefaultLogLevel();
    }

    public function publicGetLogMessage(RequestInterface $request, float $delay): string
    {
        return $this->getLogMessage($request, $delay);
    }
}

/**
 * RateLimiterTest Class
 */
class RateLimiterTest extends TestCase
{
    public function testGetDefaultLogLevelIsDebug()
    {
        $provider = new FixedAllowanceProvider(0.0);
        $limiter  = new TestableRateLimiter($provider, ['throwOnRatelimit' => false]);
        $this->assertEquals(LogLevel::DEBUG, $limiter->publicGetDefaultLogLevel());
    }

    public function testGetLogLevelReturnsDefaultWhenNotSet()
    {
        $provider = new FixedAllowanceProvider(0.0);
        $limiter  = new TestableRateLimiter($provider, ['throwOnRatelimit' => false]);
        $request  = new Request('GET', 'https://discord.com/api/v10/channels/123');
        $this->assertEquals(LogLevel::DEBUG, $limiter->publicGetLogLevel($request));
    }

    public function testSetLogLevelWithString()
    {
        $provider = new FixedAllowanceProvider(0.0);
        $limiter  = new TestableRateLimiter($provider, ['throwOnRatelimit' => false]);
        $limiter->setLogLevel(LogLevel::WARNING);

        $request = new Request('GET', 'https://discord.com/api/v10/channels/123');
        $this->assertEquals(LogLevel::WARNING, $limiter->publicGetLogLevel($request));
    }

    public function testSetLogLevelWithCallable()
    {
        $provider = new FixedAllowanceProvider(0.0);
        $limiter  = new TestableRateLimiter($provider, ['throwOnRatelimit' => false]);
        $limiter->setLogLevel(function (RequestInterface $req) {
            return LogLevel::ERROR;
        });

        $request = new Request('GET', 'https://discord.com/api/v10/channels/123');
        $this->assertEquals(LogLevel::ERROR, $limiter->publicGetLogLevel($request));
    }

    public function testGetLogMessageContainsMethodAndUri()
    {
        $provider = new FixedAllowanceProvider(0.0);
        $limiter  = new TestableRateLimiter($provider, ['throwOnRatelimit' => false]);
        $request  = new Request('GET', 'https://discord.com/api/v10/channels/123');

        $message = $limiter->publicGetLogMessage($request, 500000.0);

        $this->assertStringContainsString('GET', $message);
        $this->assertStringContainsString('https://discord.com/api/v10/channels/123', $message);
        $this->assertStringContainsString('500000', $message);
    }

    public function testThrowsRatelimitExceptionWhenOptionEnabled()
    {
        // Provider returns a large allowance so delay > 0
        $provider = new FixedAllowanceProvider(1_000_000_000.0);
        $limiter  = new RateLimiter($provider, ['throwOnRatelimit' => true]);

        $handler  = function ($request, $options) {
            // Should never reach here in this test
            return new \GuzzleHttp\Promise\FulfilledPromise(new Response(200));
        };
        $callable = $limiter($handler);
        $request  = new Request('GET', 'https://discord.com/api/v10/channels/123');

        $this->expectException(RatelimitException::class);
        $callable($request, []);
    }

    public function testPassesThroughWhenNoDelay()
    {
        $provider = new FixedAllowanceProvider(0.0);
        $limiter  = new RateLimiter($provider, ['throwOnRatelimit' => false]);

        $responseSent = false;
        $response     = new Response(200, [], '{}');

        $handler = function ($request, $options) use (&$responseSent, $response) {
            $responseSent = true;

            return new \GuzzleHttp\Promise\FulfilledPromise($response);
        };

        $callable = $limiter($handler);
        $request  = new Request('GET', 'https://discord.com/api/v10/channels/123');
        $result   = $callable($request, [])->wait();

        $this->assertTrue($responseSent);
        $this->assertSame($response, $result);
    }

    public function testRatelimitExceptionMessageContainsMethodAndUri()
    {
        $provider = new FixedAllowanceProvider(1_000_000_000.0);
        $limiter  = new RateLimiter($provider, ['throwOnRatelimit' => true]);

        $handler  = function ($request, $options) {
            return new \GuzzleHttp\Promise\FulfilledPromise(new Response(200));
        };
        $callable = $limiter($handler);
        $request  = new Request('GET', 'https://discord.com/api/v10/channels/123');

        try {
            $callable($request, []);
            $this->fail('Expected RatelimitException was not thrown');
        } catch (RatelimitException $e) {
            $this->assertStringContainsString('GET', $e->getMessage());
            $this->assertStringContainsString('https://discord.com/api/v10/channels/123', $e->getMessage());
        }
    }
}
