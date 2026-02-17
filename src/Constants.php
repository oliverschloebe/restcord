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

namespace RestCord;

/**
 * @author Aaron Scherer <aequasi@gmail.com>
 *
 * Constants Class
 */
class Constants
{
    const CDN_URL    = 'https://cdn.discordapp.com/';

    const AVATAR_URL         = self::CDN_URL.'avatars/';
    const DEFAULT_AVATAR_URL = self::CDN_URL.'embed/avatars/';
    const GUILD_ICON_URL     = self::CDN_URL.'icons/';
    const GUILD_SPLASH_URL   = self::CDN_URL.'splashes/';

    /**
     * Message flag for enabling Components V2 rendering.
     *
     * @see https://docs.discord.com/developers/components/overview
     */
    const IS_COMPONENTS_V2 = 1 << 15;

    /**
     * Component type constants.
     *
     * @see https://docs.discord.com/developers/components/reference#component-types
     */

    // Layout components
    const COMPONENT_ACTION_ROW  = 1;
    const COMPONENT_SECTION     = 9;
    const COMPONENT_SEPARATOR   = 14;
    const COMPONENT_CONTAINER   = 17;

    // Interactive components (V1)
    const COMPONENT_BUTTON             = 2;
    const COMPONENT_STRING_SELECT      = 3;
    const COMPONENT_TEXT_INPUT         = 4;
    const COMPONENT_USER_SELECT        = 5;
    const COMPONENT_ROLE_SELECT        = 6;
    const COMPONENT_MENTIONABLE_SELECT = 7;
    const COMPONENT_CHANNEL_SELECT     = 8;

    // Content components (V2)
    const COMPONENT_TEXT_DISPLAY  = 10;
    const COMPONENT_THUMBNAIL     = 11;
    const COMPONENT_MEDIA_GALLERY = 12;
    const COMPONENT_FILE          = 13;

    // Layout components (V2, modal)
    const COMPONENT_LABEL = 18;

    // Interactive components (V2)
    const COMPONENT_FILE_UPLOAD    = 19;
    const COMPONENT_RADIO_GROUP    = 21;
    const COMPONENT_CHECKBOX_GROUP = 22;
    const COMPONENT_CHECKBOX       = 23;
}
