<?php return [
    'title' => 'Бот Tg',
    'settings-group' => 'Интеграции',
    'description' => 'Бот Telegram',
    'bots' => 'Боты',
    'pages' => 'Страницы',
    'permissions' => [
        'settings' => 'Редактирование настроек Tg бота',
        'pages' => 'Редактирование страниц Tg бота'
    ],
    'settings' => [
        'primary'               => 'Основные',
        'bot_secret'            => 'Секретный ключ Bot API',
        'chat_id'               => 'ID чата оповещений',
        'channel_id'            => 'ID канала',
        'webhook_token'         => 'Секрет webhook',
        'set_webhook'           => 'Установить вебхук',
        'newsletter'            => 'Автопост',
        'meetings_section'      => 'Расписание собраний',
        'send_meetings'         => 'Публиковать расписание собраний в канал',
        'send_time'             => 'Время публикации',
        'send_meetings_header'  => 'Текст-заголовок поста',
        'send_meetings_header_comment' => 'Вступительный текст перед списком собраний, например «Расписание собраний на сегодня:»',
        'groups_link'           => 'Ссылка на полное расписание',
        'groups_link_comment'   => 'Если заполнено — под постом появится кнопка со ссылкой на полное расписание',
        'send_meetings_more'    => 'Полное расписание',
        'jft_section'           => 'Ежедневные размышления (Just For Today)',
        'send_jft'              => 'Публиковать размышление дня в канал',
        'send_jft_time'         => 'Время публикации',
        'send_jft_header'       => 'Текст-заголовок поста',
        'send_jft_header_comment' => 'Вступительный текст перед размышлением, например «Мысль на сегодня:»',
        'send_jft_more'         => 'Читать полностью'
    ],
];
