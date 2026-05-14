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
 * IconTraitTest Class
 */
class IconTraitTest extends TestCase
{
    public function testGetIconReturnsCorrectUrl()
    {
        $guild       = new Guild();
        $guild->id   = 123456789;
        $guild->icon = 'iconhash123';

        $url = $guild->getIcon();

        $this->assertEquals(
            Constants::GUILD_ICON_URL.'123456789/iconhash123.webp',
            $url
        );
    }

    public function testGetIconWithPngFormat()
    {
        $guild       = new Guild();
        $guild->id   = 987654321;
        $guild->icon = 'iconhash456';

        $url = $guild->getIcon('png');

        $this->assertEquals(
            Constants::GUILD_ICON_URL.'987654321/iconhash456.png',
            $url
        );
    }

    public function testGetIconWithJpegFormat()
    {
        $guild       = new Guild();
        $guild->id   = 111;
        $guild->icon = 'iconhash';

        $url = $guild->getIcon('jpeg');

        $this->assertEquals(
            Constants::GUILD_ICON_URL.'111/iconhash.jpeg',
            $url
        );
    }

    public function testGetIconWithSizeParameter()
    {
        $guild       = new Guild();
        $guild->id   = 222;
        $guild->icon = 'iconhash';

        $url = $guild->getIcon('webp', 64);

        $this->assertEquals(
            Constants::GUILD_ICON_URL.'222/iconhash.webp?size=64',
            $url
        );
    }

    public function testGetIconWithSizeAndFormat()
    {
        $guild       = new Guild();
        $guild->id   = 333;
        $guild->icon = 'iconhash';

        $url = $guild->getIcon('png', 1024);

        $this->assertEquals(
            Constants::GUILD_ICON_URL.'333/iconhash.png?size=1024',
            $url
        );
    }

    public function testGetIconWithoutSizeHasNoQueryString()
    {
        $guild       = new Guild();
        $guild->id   = 444;
        $guild->icon = 'iconhash';

        $url = $guild->getIcon('webp', null);

        $this->assertStringNotContainsString('?size=', $url);
    }
}
