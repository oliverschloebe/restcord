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

namespace RestCord\Model\Thread;

/**
 * Thread Model
 */
class Thread {

	/**
	 * the id of this thread
	 *
	 * @var int
	 */
	public $id;

	/**
	 * the type of thread
	 *
	 * @var int
	 */
	public $type;

	/**
	 * the id of the last message sent in this thread
	 *
	 * @var int|null
	 */
	public $last_message_id;

	/**
	 * channel flags combined as a bitfield
	 *
	 * @var int
	 */
	public $flags;

	/**
	 * the id of the guild
	 *
	 * @var int|null
	 */
	public $guild_id;

	/**
	 * the name of the thread
	 *
	 * @var string|null
	 */
	public $name;

	/**
	 * id of the parent channel
	 *
	 * @var int|null
	 */
	public $parent_id;

	/**
	 * amount of seconds a user has to wait before sending another message (0-21600)
	 *
	 * @var int|null
	 */
	public $rate_limit_per_user;

	/**
	 * the bitrate (in bits) of the voice channel
	 *
	 * @var int|null
	 */
	public $bitrate;

	/**
	 * the user limit of the voice channel
	 *
	 * @var int|null
	 */
	public $user_limit;

	/**
	 * voice region id for the voice channel
	 *
	 * @var string|null
	 */
	public $rtc_region;

	/**
	 * id of the thread creator
	 *
	 * @var int|null
	 */
	public $owner_id;

	/**
	 * thread-specific fields not needed by other channels
	 *
	 * @var array|null
	 */
	public $thread_metadata;

	/**
	 * number of messages in a thread
	 *
	 * @var int|null
	 */
	public $message_count;

	/**
	 * an approximate count of the number of members in the thread
	 *
	 * @var int|null
	 */
	public $member_count;

	/**
	 * number of messages ever sent in a thread
	 *
	 * @var int|null
	 */
	public $total_message_sent;

	/**
	 * @param array $content
	 */
	public function __construct(array $content = null) {
		if (null === $content) {
		    return;
		}
		                    
		foreach ($content as $key => $value) {
		    if (property_exists($this, $key)) {
		        $this->{$key} = $value;
		    }
		}
	}
}
