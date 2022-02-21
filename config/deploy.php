<?php

return [
    'name' => 'na-cms',

    'current' => env('DEPLOY_ENV', 'prod_samara'),

    'environments' => [
        'prod_samara' => [
            'ssh_host'       => env('DEPLOY_SSH_HOST', 'example.com'),
            'ssh_user'       => env('DEPLOY_SSH_USER', 'forge'),
            'deploy_path'    => env('DEPLOY_PATH', '/srv/laravel'),
            'repository_url' => env('DEPLOY_GIT_URL', 'git@github.com:example/example.git'),
            'tag'            => env('DEPLOY_TAG', 'v0.1.0'),
            'linked_files'   => ['.env'],
            'linked_dirs'    => ['storage/app', 'storage/framework', 'storage/logs'],
            'copied_dirs'    => ['node_modules', 'vendor'],
        ],
        'prod_ekb' => [
            'ssh_host'       => env('DEPLOY_SSH_HOST_2', 'example.com'),
            'ssh_user'       => env('DEPLOY_SSH_USER_2', 'forge'),
            'deploy_path'    => env('DEPLOY_PATH_2', '/srv/laravel'),
            'repository_url' => env('DEPLOY_GIT_URL', 'git@github.com:example/example.git'),
            'tag'            => env('DEPLOY_TAG', 'v0.1.0'),
            'linked_files'   => ['.env'],
            'linked_dirs'    => ['storage/app', 'storage/framework', 'storage/logs'],
            'copied_dirs'    => ['node_modules', 'vendor'],
        ]
    ],

    'telegram' => [
        'token'     => env('DEPLOY_TG_BOT_TOKEN'),
        'chat_id'   => env('DEPLOY_TG_BOT_CHAT_ID'),
    ],
];