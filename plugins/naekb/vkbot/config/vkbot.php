<?php

use NAEkb\VkBot\Classes\PostAddCommand;
use NAEkb\VkBot\Classes\CleanTimeCommand;
use NAEkb\VkBot\Classes\StatisticsCommand;
use NAEkb\VkBot\Classes\StartCommand;
use NAEkb\VkBot\Classes\HelpCommand;
use VK\OAuth\Scopes\VKOAuthGroupScope;
use VK\OAuth\Scopes\VKOAuthUserScope;

return [
    'api_version'       => '5.131',
    'app_client_id'     => 51576532,
    'api_oauth_url'     => 'https://oauth.vk.ru/authorize',
    'api_token_url'     => 'https://oauth.vk.ru/access_token',
    'app_client_secret' => 'sKjJLddeHXNsa8NzdaI8',
    'scope'             => [
        VKOAuthUserScope::WALL,
        VKOAuthUserScope::OFFLINE,
        VKOAuthUserScope::STATS,
        VKOAuthUserScope::PHOTOS,
        VKOAuthUserScope::AUDIO,
        VKOAuthUserScope::VIDEO,
        VKOAuthUserScope::DOCS,
        VKOAuthUserScope::GROUPS
    ],
    'scope_group'       => [
        VKOAuthGroupScope::PHOTOS,
        VKOAuthGroupScope::MESSAGES,
        VKOAuthGroupScope::DOCS,
        VKOAuthGroupScope::MANAGE
    ],
    'commands'          => [
        'PostAdd'           => PostAddCommand::class,
        'CleanTime'         => CleanTimeCommand::class,
        'Statistics'        => StatisticsCommand::class,
        'Start'             => StartCommand::class,
        'Help'              => HelpCommand::class
    ]
];
