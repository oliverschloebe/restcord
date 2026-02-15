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
 * Application Command Intellisense Helper
 */
interface ApplicationCommand {

	/**
	 * @see https://discord.com/developers/docs/interactions/application-commands#get-global-application-commands
	 *
	 * @param array $options ['application.id' => 'snowflake', 'with_localizations' => 'boolean']
	 * @return \GuzzleHttp\Command\Result Returns an array of application command objects.
	 */
	public function getGlobalApplicationCommands(array $options);

	/**
	 * @see https://discord.com/developers/docs/interactions/application-commands#create-global-application-command
	 *
	 * @param array $options ['application.id' => 'snowflake', 'name' => 'string', 'description' => 'string', 'options' => 'array', 'default_member_permissions' => 'string', 'dm_permission' => 'boolean', 'type' => 'integer', 'name_localizations' => 'object', 'description_localizations' => 'object']
	 * @return \GuzzleHttp\Command\Result Returns the newly created application command object.
	 */
	public function createGlobalApplicationCommand(array $options);

	/**
	 * @see https://discord.com/developers/docs/interactions/application-commands#bulk-overwrite-global-application-commands
	 *
	 * @param array $options ['application.id' => 'snowflake', 'commands' => 'array']
	 * @return \GuzzleHttp\Command\Result Returns an array of application command objects.
	 */
	public function bulkOverwriteGlobalApplicationCommands(array $options);

	/**
	 * @see https://discord.com/developers/docs/interactions/application-commands#get-guild-application-commands
	 *
	 * @param array $options ['application.id' => 'snowflake', 'guild.id' => 'snowflake', 'with_localizations' => 'boolean']
	 * @return \GuzzleHttp\Command\Result Returns an array of application command objects.
	 */
	public function getGuildApplicationCommands(array $options);

	/**
	 * @see https://discord.com/developers/docs/interactions/application-commands#create-guild-application-command
	 *
	 * @param array $options ['application.id' => 'snowflake', 'guild.id' => 'snowflake', 'name' => 'string', 'description' => 'string', 'options' => 'array', 'default_member_permissions' => 'string', 'type' => 'integer', 'name_localizations' => 'object', 'description_localizations' => 'object']
	 * @return \GuzzleHttp\Command\Result Returns the newly created application command object.
	 */
	public function createGuildApplicationCommand(array $options);

	/**
	 * @see https://discord.com/developers/docs/interactions/application-commands#bulk-overwrite-guild-application-commands
	 *
	 * @param array $options ['application.id' => 'snowflake', 'guild.id' => 'snowflake', 'commands' => 'array']
	 * @return \GuzzleHttp\Command\Result Returns an array of application command objects.
	 */
	public function bulkOverwriteGuildApplicationCommands(array $options);
}
