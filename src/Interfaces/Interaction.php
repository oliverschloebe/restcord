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

namespace RestCord\Interfaces;

/**
 * Interaction Intellisense Helper
 */
interface Interaction {

	/**
	 * @see https://discord.com/developers/docs/interactions/receiving-and-responding#create-interaction-response
	 *
	 * @param array $options ['interaction.id' => 'snowflake', 'interaction.token' => 'string', 'type' => 'integer', 'data' => 'object']
	 * @return \GuzzleHttp\Command\Result Returns a 204 empty response on success.
	 */
	public function createInteractionResponse(array $options);

	/**
	 * @see https://discord.com/developers/docs/interactions/receiving-and-responding#get-original-interaction-response
	 *
	 * @param array $options ['application.id' => 'snowflake', 'interaction.token' => 'string']
	 * @return \GuzzleHttp\Command\Result Returns a message object.
	 */
	public function getOriginalInteractionResponse(array $options);

	/**
	 * @see https://discord.com/developers/docs/interactions/receiving-and-responding#edit-original-interaction-response
	 *
	 * @param array $options ['application.id' => 'snowflake', 'interaction.token' => 'string', 'content' => 'string', 'embeds' => 'array', 'components' => 'array', 'allowed_mentions' => 'object', 'attachments' => 'array']
	 * @return \GuzzleHttp\Command\Result Returns the edited message object.
	 */
	public function editOriginalInteractionResponse(array $options);

	/**
	 * @see https://discord.com/developers/docs/interactions/receiving-and-responding#delete-original-interaction-response
	 *
	 * @param array $options ['application.id' => 'snowflake', 'interaction.token' => 'string']
	 * @return \GuzzleHttp\Command\Result Returns a 204 empty response on success.
	 */
	public function deleteOriginalInteractionResponse(array $options);

	/**
	 * @see https://discord.com/developers/docs/interactions/receiving-and-responding#create-followup-message
	 *
	 * @param array $options ['application.id' => 'snowflake', 'interaction.token' => 'string', 'content' => 'string', 'embeds' => 'array', 'components' => 'array', 'allowed_mentions' => 'object', 'attachments' => 'array', 'flags' => 'integer']
	 * @return \GuzzleHttp\Command\Result Returns the followup message object.
	 */
	public function createFollowupMessage(array $options);

	/**
	 * @see https://discord.com/developers/docs/interactions/receiving-and-responding#get-followup-message
	 *
	 * @param array $options ['application.id' => 'snowflake', 'interaction.token' => 'string', 'message.id' => 'snowflake']
	 * @return \GuzzleHttp\Command\Result Returns a followup message object.
	 */
	public function getFollowupMessage(array $options);

	/**
	 * @see https://discord.com/developers/docs/interactions/receiving-and-responding#edit-followup-message
	 *
	 * @param array $options ['application.id' => 'snowflake', 'interaction.token' => 'string', 'message.id' => 'snowflake', 'content' => 'string', 'embeds' => 'array', 'components' => 'array', 'allowed_mentions' => 'object', 'attachments' => 'array']
	 * @return \GuzzleHttp\Command\Result Returns the edited followup message object.
	 */
	public function editFollowupMessage(array $options);

	/**
	 * @see https://discord.com/developers/docs/interactions/receiving-and-responding#delete-followup-message
	 *
	 * @param array $options ['application.id' => 'snowflake', 'interaction.token' => 'string', 'message.id' => 'snowflake']
	 * @return \GuzzleHttp\Command\Result Returns a 204 empty response on success.
	 */
	public function deleteFollowupMessage(array $options);
}
