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
use RestCord\Model\User\User;

/**
 * AvatarTraitTest Class
 */
class AvatarTraitTest extends TestCase
{
    public function testGetAvatarReturnsDefaultWhenNoAvatarSet()
    {
        $user                = new User();
        $user->avatar        = null;
        $user->id            = 123456789;
        $user->discriminator = 1234;

        $url = $user->getAvatar();

        // discriminator % 5 = 1234 % 5 = 4
        $this->assertEquals(Constants::DEFAULT_AVATAR_URL.'4.png', $url);
    }

    public function testGetAvatarReturnsDefaultWithVariousDiscriminators()
    {
        $cases = [
            ['discriminator' => 0, 'expected_index' => 0],
            ['discriminator' => 1, 'expected_index' => 1],
            ['discriminator' => 5, 'expected_index' => 0],
            ['discriminator' => 6, 'expected_index' => 1],
            ['discriminator' => 9, 'expected_index' => 4],
        ];

        foreach ($cases as $case) {
            $user                = new User();
            $user->avatar        = null;
            $user->id            = 100;
            $user->discriminator = $case['discriminator'];

            $url = $user->getAvatar();
            $this->assertEquals(
                Constants::DEFAULT_AVATAR_URL.$case['expected_index'].'.png',
                $url,
                "Failed for discriminator {$case['discriminator']}"
            );
        }
    }

    public function testGetAvatarReturnsUserAvatarUrlWhenAvatarIsSet()
    {
        $user                = new User();
        $user->avatar        = 'abcdef1234567890';
        $user->id            = 987654321;
        $user->discriminator = 0001;

        $url = $user->getAvatar();

        $this->assertEquals(
            Constants::AVATAR_URL.'987654321/abcdef1234567890.webp',
            $url
        );
    }

    public function testGetAvatarWithExplicitFormat()
    {
        $user                = new User();
        $user->avatar        = 'hash123';
        $user->id            = 111;
        $user->discriminator = 0;

        $url = $user->getAvatar('png');

        $this->assertEquals(Constants::AVATAR_URL.'111/hash123.png', $url);
    }

    public function testGetAvatarWithSizeParameter()
    {
        $user                = new User();
        $user->avatar        = 'hash123';
        $user->id            = 111;
        $user->discriminator = 0;

        $url = $user->getAvatar('webp', 128);

        $this->assertEquals(Constants::AVATAR_URL.'111/hash123.webp?size=128', $url);
    }

    public function testGetAvatarReturnsGifForAnimatedAvatar()
    {
        $user                = new User();
        $user->avatar        = 'a_animatedhash123';
        $user->id            = 222;
        $user->discriminator = 0;

        $url = $user->getAvatar();

        $this->assertStringContainsString('.gif', $url);
        $this->assertEquals(Constants::AVATAR_URL.'222/a_animatedhash123.gif', $url);
    }

    public function testGetAvatarAnimatedAvatarWithSize()
    {
        $user                = new User();
        $user->avatar        = 'a_animatedhash';
        $user->id            = 333;
        $user->discriminator = 0;

        $url = $user->getAvatar('webp', 256);

        // Animated avatars override format to gif
        $this->assertEquals(Constants::AVATAR_URL.'333/a_animatedhash.gif?size=256', $url);
    }

    public function testGetAvatarDefaultHasNoSizeWhenSizeIsNull()
    {
        $user                = new User();
        $user->avatar        = null;
        $user->id            = 100;
        $user->discriminator = 0;

        $url = $user->getAvatar('webp', null);

        $this->assertStringNotContainsString('?size=', $url);
    }

    public function testGetAvatarUserAvatarHasNoSizeWhenSizeIsNull()
    {
        $user                = new User();
        $user->avatar        = 'hash';
        $user->id            = 100;
        $user->discriminator = 0;

        $url = $user->getAvatar('webp', null);

        $this->assertStringNotContainsString('?size=', $url);
    }
}
