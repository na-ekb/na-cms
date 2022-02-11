<?php

namespace Modules\TgBot\Tests\Data;

class Commands
{
    public static function message(?string $text = '/start', int $length = 6): array
    {
        $message = [
            'update_id' => 400502026,
            'message'   => [
                'message_id' => 39696,
                'from'       => [
                    'id'            => 244563726,
                    'is_bot'        => false,
                    'first_name'    => 'Niko',
                    'last_name'     => 'Las',
                    'username'      => 'NikoVonLas',
                    'language_code' => 'ru'
                ],
                'chat'      => [
                    'id'            => 244563726,
                    'first_name'    => 'Niko',
                    'last_name'     => 'Las',
                    'username'      => 'NikoVonLas',
                    'type'          => 'private'
                ],
                'date'      => time(),
                'text'      => $text,
                'entities'  => [
                    [
                        'offset'    => 0,
                        'length'    => $length,
                        'type'      => 'bot_command'
                    ]
                ]
            ]
        ];

        if (!empty($array)) {
            return array_merge_recursive($message, $array);
        }

        return $message;
    }

    public static function callbackQuery() {
        return [

        ];
    }

    public static function geo() {
        return [
            'update_id' => 309747145,
            'message'   => [
                'message_id' => 269,
                'from'       => [
                    'id'            => 244563726,
                    'is_bot'        => false,
                    'first_name'    => 'Niko',
                    'last_name'     => 'Las',
                    'username'      => 'NikoVonLas',
                    'language_code' => 'ru'
                ],
                'chat'      => [
                    'id'            => 244563726,
                    'first_name'    => 'Niko',
                    'last_name'     => 'Las',
                    'username'      => 'NikoVonLas',
                    'type'          => 'private'
                ],
                'date'      => time(),
                'location'  => [
                    'latitude'  => 53.201147,
                    'longitude' => 50.150907
                ]

            ]
        ];
    }
}
