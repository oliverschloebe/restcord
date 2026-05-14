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

/**
 * ConstantsTest Class
 */
class ConstantsTest extends TestCase
{
    public function testCdnUrl()
    {
        $this->assertEquals('https://cdn.discordapp.com/', Constants::CDN_URL);
    }

    public function testAvatarUrl()
    {
        $this->assertEquals(Constants::CDN_URL.'avatars/', Constants::AVATAR_URL);
    }

    public function testDefaultAvatarUrl()
    {
        $this->assertEquals(Constants::CDN_URL.'embed/avatars/', Constants::DEFAULT_AVATAR_URL);
    }

    public function testGuildIconUrl()
    {
        $this->assertEquals(Constants::CDN_URL.'icons/', Constants::GUILD_ICON_URL);
    }

    public function testGuildSplashUrl()
    {
        $this->assertEquals(Constants::CDN_URL.'splashes/', Constants::GUILD_SPLASH_URL);
    }

    public function testIsComponentsV2FlagValue()
    {
        $this->assertEquals(1 << 15, Constants::IS_COMPONENTS_V2);
        $this->assertEquals(32768, Constants::IS_COMPONENTS_V2);
    }

    public function testComponentTypeConstants()
    {
        $this->assertEquals(1, Constants::COMPONENT_ACTION_ROW);
        $this->assertEquals(2, Constants::COMPONENT_BUTTON);
        $this->assertEquals(3, Constants::COMPONENT_STRING_SELECT);
        $this->assertEquals(4, Constants::COMPONENT_TEXT_INPUT);
        $this->assertEquals(5, Constants::COMPONENT_USER_SELECT);
        $this->assertEquals(6, Constants::COMPONENT_ROLE_SELECT);
        $this->assertEquals(7, Constants::COMPONENT_MENTIONABLE_SELECT);
        $this->assertEquals(8, Constants::COMPONENT_CHANNEL_SELECT);
        $this->assertEquals(9, Constants::COMPONENT_SECTION);
        $this->assertEquals(10, Constants::COMPONENT_TEXT_DISPLAY);
        $this->assertEquals(11, Constants::COMPONENT_THUMBNAIL);
        $this->assertEquals(12, Constants::COMPONENT_MEDIA_GALLERY);
        $this->assertEquals(13, Constants::COMPONENT_FILE);
        $this->assertEquals(14, Constants::COMPONENT_SEPARATOR);
        $this->assertEquals(17, Constants::COMPONENT_CONTAINER);
        $this->assertEquals(18, Constants::COMPONENT_LABEL);
        $this->assertEquals(19, Constants::COMPONENT_FILE_UPLOAD);
        $this->assertEquals(21, Constants::COMPONENT_RADIO_GROUP);
        $this->assertEquals(22, Constants::COMPONENT_CHECKBOX_GROUP);
        $this->assertEquals(23, Constants::COMPONENT_CHECKBOX);
    }

    public function testButtonStyleConstants()
    {
        $this->assertEquals(1, Constants::BUTTON_PRIMARY);
        $this->assertEquals(2, Constants::BUTTON_SECONDARY);
        $this->assertEquals(3, Constants::BUTTON_SUCCESS);
        $this->assertEquals(4, Constants::BUTTON_DANGER);
        $this->assertEquals(5, Constants::BUTTON_LINK);
        $this->assertEquals(6, Constants::BUTTON_PREMIUM);
    }

    public function testButtonStylesAreDistinct()
    {
        $styles = [
            Constants::BUTTON_PRIMARY,
            Constants::BUTTON_SECONDARY,
            Constants::BUTTON_SUCCESS,
            Constants::BUTTON_DANGER,
            Constants::BUTTON_LINK,
            Constants::BUTTON_PREMIUM,
        ];
        $this->assertCount(count($styles), array_unique($styles));
    }

    public function testComponentTypesAreDistinct()
    {
        $types = [
            Constants::COMPONENT_ACTION_ROW,
            Constants::COMPONENT_BUTTON,
            Constants::COMPONENT_STRING_SELECT,
            Constants::COMPONENT_TEXT_INPUT,
            Constants::COMPONENT_USER_SELECT,
            Constants::COMPONENT_ROLE_SELECT,
            Constants::COMPONENT_MENTIONABLE_SELECT,
            Constants::COMPONENT_CHANNEL_SELECT,
            Constants::COMPONENT_SECTION,
            Constants::COMPONENT_TEXT_DISPLAY,
            Constants::COMPONENT_THUMBNAIL,
            Constants::COMPONENT_MEDIA_GALLERY,
            Constants::COMPONENT_FILE,
            Constants::COMPONENT_SEPARATOR,
            Constants::COMPONENT_CONTAINER,
            Constants::COMPONENT_LABEL,
            Constants::COMPONENT_FILE_UPLOAD,
            Constants::COMPONENT_RADIO_GROUP,
            Constants::COMPONENT_CHECKBOX_GROUP,
            Constants::COMPONENT_CHECKBOX,
        ];
        $this->assertCount(count($types), array_unique($types));
    }
}
