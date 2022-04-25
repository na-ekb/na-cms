<?php

namespace Modules\TgBot\Classes;

use Telegram\Bot\Objects\BaseObject;

/**
 * Class WebAppInfo https://core.telegram.org/bots/api#webappinfo
 *
 *
 * @property string $url    An HTTPS URL of a Web App to be opened with additional data as specified in Initializing Web Apps
 */
class WebAppInfo extends BaseObject
{
    /**
     * {@inheritdoc}
     */
    public function relations()
    {
        return [];
    }
}
