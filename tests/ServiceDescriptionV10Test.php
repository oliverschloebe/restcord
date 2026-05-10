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

use GuzzleHttp\Command\Result;
use PHPUnit\Framework\TestCase;
use RestCord\DiscordClient;

/**
 * ServiceDescriptionV10Test Class.
 *
 * Tests for Discord API v10 compatibility in service_description-v10.json
 */
class ServiceDescriptionV10Test extends TestCase
{
    /**
     * @var array
     */
    public $description;

    /**
     * @var DiscordClient
     */
    public $client;

    public function setUp(): void
    {
        $this->description = json_decode(
            file_get_contents(__DIR__.'/../src/Resources/service_description-v10.json'),
            true
        );
        $this->client      = new DiscordClient(['token' => 'fake-token', 'version' => 10]);
    }

    public function testBaseUri()
    {
        $this->assertEquals('https://discord.com/api/v10', $this->description['baseUri']);
    }

    public function testVersion()
    {
        $this->assertEquals(10, $this->description['version']);
    }

    /**
     * Test that v10-specific changes are present
     * Key change in v10: embed (singular) changed to embeds (plural) for messages.
     */
    public function testV10EmbedsParameter()
    {
        // createMessage should have 'embeds' parameter, not 'embed'
        $this->assertArrayHasKey('embeds', $this->description['operations']['channel']['createMessage']['parameters']);
        $this->assertArrayNotHasKey('embed', $this->description['operations']['channel']['createMessage']['parameters']);

        // editMessage should have 'embeds' parameter, not 'embed'
        $this->assertArrayHasKey('embeds', $this->description['operations']['channel']['editMessage']['parameters']);
        $this->assertArrayNotHasKey('embed', $this->description['operations']['channel']['editMessage']['parameters']);

        // Verify embeds is an array type
        $this->assertEquals('array', $this->description['operations']['channel']['createMessage']['parameters']['embeds']['type']);
        $this->assertEquals('array', $this->description['operations']['channel']['editMessage']['parameters']['embeds']['type']);
    }

    /**
     * Test that v9-specific operations still exist in v10.
     */
    public function testV9OperationsExistInV10()
    {
        $this->assertArrayHasKey('startThreadFromMessage', $this->description['operations']['channel']);
        $this->assertArrayHasKey('startThreadInForumOrMediaChannel', $this->description['operations']['channel']);
        $this->assertArrayHasKey('createInteractionResponse', $this->description['operations']['interaction']);
        $this->assertArrayHasKey('getGlobalApplicationCommands', $this->description['operations']['application-command']);
    }

    public function testStartThreadInForumOrMediaChannelParameters()
    {
        $operation = $this->description['operations']['channel']['startThreadInForumOrMediaChannel'];

        $this->assertEquals('/channels/{channel.id}/threads', $operation['url']);
        $this->assertArrayHasKey('message', $operation['parameters']);
        $this->assertTrue($operation['parameters']['message']['required']);
        $this->assertEquals('array', $operation['parameters']['message']['type']);
        $this->assertArrayHasKey('applied_tags', $operation['parameters']);
        $this->assertArrayHasKey('flags', $operation['parameters']);
    }

    /**
     * Test that all operation resources map to valid PHP interfaces and methods.
     */
    public function testOperationResources()
    {
        foreach ($this->description['operations'] as $resource => $operations) {
            $resource = str_replace(' ', '', ucwords(str_replace(['-', '_'], ' ', $resource)));
            $class    = '\\RestCord\\Interfaces\\'.ucwords($resource);
            $this->assertTrue(interface_exists($class), 'Could not find interface: '.$class);

            $refl = new \ReflectionClass($class);
            foreach ($operations as $method => $operation) {
                $this->assertTrue($refl->hasMethod($method), "Could not find method $method in interface $class");
                $reflMethod = $refl->getMethod($method);
                if (isset($operation['responseTypes']) && count($operation['responseTypes']) >= 1) {
                    $firstType = $operation['responseTypes'][0]['type'];
                    $array     = stripos($firstType, 'Array<') !== false;
                    if ($array) {
                        $firstType = substr($firstType, 6, -1);
                    }
                    $firstType = explode('/', $firstType);

                    $returnType = sprintf(
                        '\\RestCord\\Model\\%s\\%s',
                        str_replace(
                            ' ',
                            '',
                            ucwords(str_replace('-', ' ', $firstType[0]))
                        ),
                        str_replace(
                            ' ',
                            '',
                            ucwords(str_replace('-', ' ', $firstType[1]))
                        )
                    );

                    $returnType = $this->mapBadDocs($returnType);

                    if (!class_exists($returnType)) {
                        $returnType = '\\'.Result::class;
                    }

                    $returnType .= $array ? '[]' : '';

                    $this->assertEquals($returnType, $this->getReturnType($reflMethod));
                }
            }
        }
    }

    /**
     * Test that all model resources map to valid PHP model classes.
     */
    public function testModels()
    {
        foreach ($this->description['models'] as $resource => $models) {
            $resource  = str_replace(' ', '', ucwords(str_replace(['-', '_'], ' ', $resource)));
            $namespace = '\\RestCord\\Model\\'.ucwords($resource).'\\';

            foreach ($models as $method => $data) {
                $class = $namespace.ucwords($method);
                $this->assertTrue(class_exists($class), 'Could not find model class: '.$class);
                $refl = new \ReflectionClass($class);
                foreach ($data['properties'] as $name => $info) {
                    $name = lcfirst(str_replace([' ', '?'], '', str_replace('-', '_', $name)));
                    $this->assertTrue($refl->hasProperty($name), "Cannot find property $name on $class");
                }
            }
        }
    }

    /**
     * Test that critical Discord API v10 operations are present.
     */
    public function testCriticalV10Operations()
    {
        // Guild operations
        $this->assertArrayHasKey('createGuild', $this->description['operations']['guild']);
        $this->assertArrayHasKey('getGuild', $this->description['operations']['guild']);
        $this->assertArrayHasKey('modifyGuild', $this->description['operations']['guild']);

        // Channel operations
        $this->assertArrayHasKey('getChannel', $this->description['operations']['channel']);
        $this->assertArrayHasKey('modifyChannel', $this->description['operations']['channel']);
        $this->assertArrayHasKey('createMessage', $this->description['operations']['channel']);
        $this->assertArrayHasKey('editMessage', $this->description['operations']['channel']);

        // User operations
        $this->assertArrayHasKey('getCurrentUser', $this->description['operations']['user']);

        // Application command operations (slash commands)
        $this->assertArrayHasKey('getGlobalApplicationCommands', $this->description['operations']['application-command']);
        $this->assertArrayHasKey('createGlobalApplicationCommand', $this->description['operations']['application-command']);
    }

    /**
     * Test that webhook operations contain embeds parameter (plural).
     */
    public function testWebhookEmbedsParameter()
    {
        // executeWebhook should have 'embeds' parameter
        if (isset($this->description['operations']['webhook']['executeWebhook']['parameters'])) {
            $this->assertArrayHasKey('embeds', $this->description['operations']['webhook']['executeWebhook']['parameters']);
        }

        // executeSlackCompatibleWebhook should have 'embeds' parameter
        if (isset($this->description['operations']['webhook']['executeSlackCompatibleWebhook']['parameters'])) {
            $params = $this->description['operations']['webhook']['executeSlackCompatibleWebhook']['parameters'];
            if (isset($params['embeds']) || isset($params['embed'])) {
                $this->assertArrayHasKey('embeds', $params, 'Slack webhook should use embeds (plural)');
            }
        }
    }

    private function mapBadDocs($cls)
    {
        switch ($cls) {
            case '\RestCord\Model\User\DmChannel':
                $cls = '\RestCord\Model\Channel\DmChannel';
                break;
            case '\RestCord\Model\Channel\Invite':
            case '\RestCord\Model\Guild\Invite':
                $cls = '\RestCord\Model\Invite\Invite';
                break;
            case '\RestCord\Model\Guild\GuildChannel':
                $cls = '\RestCord\Model\Channel\GuildChannel';
                break;
            case '\RestCord\Model\Guild\User':
            case '\RestCord\Model\Channel\User':
                $cls = '\RestCord\Model\User\User';
                break;
            default:
                return $cls;
        }

        return $cls;
    }

    private function getReturnType(\ReflectionMethod $reflMethod)
    {
        $comment = $reflMethod->getDocComment();
        $regex   = '/@return ([\\\\A-Za-z\[\]]+)/';
        preg_match($regex, $comment, $matches);
        if (empty($matches)) {
            return;
        }

        return $matches[1];
    }
}
