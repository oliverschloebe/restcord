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
 * Thread Member Model
 */
class Member {

	/**
	 * id of the thread
	 *
	 * @var int|null
	 */
	public $id;

	/**
	 * id of the user
	 *
	 * @var int|null
	 */
	public $user_id;

	/**
	 * the time the current user last joined the thread
	 *
	 * @var string|null
	 */
	public $join_timestamp;

	/**
	 * any user-thread settings, currently only used for notifications
	 *
	 * @var int
	 */
	public $flags;

	/**
	 * whether the thread is muted
	 *
	 * @var bool|null
	 */
	public $muted;

	/**
	 * mute configuration
	 *
	 * @var array|null
	 */
	public $mute_config;

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
