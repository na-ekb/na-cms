<?php

return [
    'name' => 'na-cms',

    'current' => env('DEPLOY_ENV', 'production'),

    'environments' => [
        'production' => [
            'ssh_host'       => env('DEPLOY_SSH_HOST', 'example.com'),
            'ssh_user'       => env('DEPLOY_SSH_USER', 'forge'),
            'deploy_path'    => env('DEPLOY_PATH', '/srv/laravel'),
            'repository_url' => env('DEPLOY_GIT_URL', 'git@github.com:example/example.git'),
            'linked_files'   => ['.env'],
            'linked_dirs'    => ['storage/app', 'storage/framework', 'storage/logs'],
            'copied_dirs'    => ['node_modules', 'vendor'],
        ],
        'local'     => [
            'ssh_host'       => '127.0.0.1',
        ]
    ],

    'telegram' => [
        'token'     => env('DEPLOY_TG_BOT_TOKEN'),
        'chat_id'   => env('DEPLOY_TG_BOT_CHAT_ID'),
    ],
];