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

use PHPUnit\Framework\TestCase;
use RestCord\Constants;
use RestCord\Model\Guild\Guild;

/**
 * SplashTraitTest Class
 */
class SplashTraitTest extends TestCase
{
    public function testGetSplashReturnsCorrectUrl()
    {
        $guild         = new Guild();
        $guild->id     = 123456789;
        $guild->splash = 'splashhash123';

        $url = $guild->getSplash();

        $this->assertEquals(
            Constants::GUILD_SPLASH_URL.'123456789/splashhash123.webp',
            $url
        );
    }

    public function testGetSplashWithPngFormat()
    {
        $guild         = new Guild();
        $guild->id     = 987654321;
        $guild->splash = 'splashhash456';

        $url = $guild->getSplash('png');

        $this->assertEquals(
            Constants::GUILD_SPLASH_URL.'987654321/splashhash456.png',
            $url
        );
    }

    public function testGetSplashWithJpegFormat()
    {
        $guild         = new Guild();
        $guild->id     = 111;
        $guild->splash = 'splashhash';

        $url = $guild->getSplash('jpeg');

        $this->assertEquals(
            Constants::GUILD_SPLASH_URL.'111/splashhash.jpeg',
            $url
        );
    }

    public function testGetSplashWithSizeParameter()
    {
        $guild         = new Guild();
        $guild->id     = 222;
        $guild->splash = 'splashhash';

        $url = $guild->getSplash('webp', 128);

        $this->assertEquals(
            Constants::GUILD_SPLASH_URL.'222/splashhash.webp?size=128',
            $url
        );
    }

    public function testGetSplashWithSizeAndFormat()
    {
        $guild         = new Guild();
        $guild->id     = 333;
        $guild->splash = 'splashhash';

        $url = $guild->getSplash('png', 512);

        $this->assertEquals(
            Constants::GUILD_SPLASH_URL.'333/splashhash.png?size=512',
            $url
        );
    }

    public function testGetSplashWithoutSizeHasNoQueryString()
    {
        $guild         = new Guild();
        $guild->id     = 444;
        $guild->splash = 'splashhash';

        $url = $guild->getSplash('webp', null);

        $this->assertStringNotContainsString('?size=', $url);
    }
}
