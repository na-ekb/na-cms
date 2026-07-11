<?php return [
    'title' => 'Tg bot',
    'settings-group' => 'Integrations',
    'description' => 'Bot for Telegram',
    'bots' => 'Bots',
    'pages' => 'Pages',
    'permissions' => [
        'settings' => 'Manage Tg Bot settings',
        'pages' => 'Manage Tg Bot pages'
    ],
    'settings' => [
        'primary'               => 'Primary',
        'bot_secret'            => 'Bot API token',
        'chat_id'               => 'Notifications chat ID',
        'channel_id'            => 'Channel ID',
        'webhook_token'         => 'Webhook secret',
        'set_webhook'           => 'Set webhook for bot',
        'newsletter'            => 'Autopost',
        'meetings_section'      => 'Meetings schedule',
        'send_meetings'         => 'Post meetings schedule to the channel',
        'send_time'             => 'Post time',
        'send_meetings_header'  => 'Post header text',
        'send_meetings_header_comment' => 'Intro text before the meetings list, e.g. "Today\'s meetings schedule:"',
        'groups_link'           => 'Full schedule link',
        'groups_link_comment'   => 'If set, a button linking to the full schedule is added below the post',
        'send_meetings_more'    => 'Full schedule',
        'jft_section'           => 'Just For Today reflections',
        'send_jft'              => 'Post the daily reflection to the channel',
        'send_jft_time'         => 'Post time',
        'send_jft_header'       => 'Post header text',
        'send_jft_header_comment' => 'Intro text before the reflection, e.g. "Just for today:"',
        'send_jft_more'         => 'Read the full text'
    ],
];
