<?php namespace NAEkb\VkBot\Classes;

use DirectoryIterator;

use Illuminate\Support\Carbon;
use Carbon\Exceptions\InvalidFormatException;

use NAEkb\VKBot\Models\Photo;
use VK\Exceptions\VKApiException;
use VK\Exceptions\VKClientException;

use function morphos\Russian\pluralize;
use function morphos\English\pluralize as engPluralize;

use NAEkb\VKBot\Models\CleanDate;
use NAEkb\VkBot\Models\VkSettings;

class CleanTimeCommand extends AbstractCommand
{
    /** @inheritdoc  */
    public array $text = [
        'parse',
        'photo'
    ];

    /** @inheritdoc */
    protected string $name = 'cleanTime';

    /** @inheritdoc */
    protected function main()
    {
        if (CleanDate::where('user_id', $this->userId)->exists()) {
            $this->start();
        } else {
            $this->set();
        }
    }

    protected function start() {
        $date = $this->humanDate(CleanDate::where('user_id', $this->userId)->first());

        if (empty($date)) {
            $date = __('naekb.vkbot::lang.commands.clean_time.jft');
        }

        $keyboard = [
            'inline'    => true,
            'buttons'   => [
                [
                    [
                        'action' => [
                            'type'      => 'callback',
                            'payload'   => json_encode([
                                'command'   => 'cleanTime',
                                'action'    => 'del'
                            ], JSON_UNESCAPED_SLASHES),
                            'label'     => __('naekb.vkbot::lang.commands.clean_time.delete')
                        ]
                    ]
                ]
            ]
        ];

        $this->reply(__('naekb.vkbot::lang.commands.clean_time.clean', [
            'date' => $date
        ]), $keyboard, true, false);
    }

    protected function set()
    {
        $keyboard = [
            'inline'    => true,
            'buttons'   => [
                [
                    [
                        'action' => [
                            'type'      => 'callback',
                            'payload'   => json_encode([
                                'command'   => 'cleanTime',
                                'action'    => 'parse'
                            ], JSON_UNESCAPED_SLASHES),
                            'label'     => __('naekb.vkbot::lang.commands.clean_time.set_btn')
                        ]
                    ]
                ]
            ]
        ];

        $this->reply(__('naekb.vkbot::lang.commands.clean_time.set_channel'), $keyboard, false);
    }

    public function parse()
    {
        if (!empty($this->object['message']) && !empty($this->object['message']->text)) {
            try {
                $time = Carbon::parse($this->object['message']->text)->startOfDay();
                if ($time->lessThan(Carbon::now())) {
                    CleanDate::updateOrCreate([
                        'user_id' => $this->userId
                    ], [
                        'date'          => $time,
                        'updated_at'    => Carbon::today()->subDay()
                    ]);
                    $text = __('naekb.vkbot::lang.commands.clean_time.setted_channel');
                    $this->setCommandState();
                } else {
                    $text = __('naekb.vkbot::lang.commands.clean_time.error_future');
                }

                $date = $this->humanDate(CleanDate::where('user_id', $this->userId)->first());
                if (empty(!$date)) {
                    $text .= "\r\n" . __('naekb.vkbot::lang.commands.clean_time.clean', [
                        'date' => $date
                    ]);
                }
            } catch (\Throwable $e) {
                report($e);
                if (!is_a($e, InvalidFormatException::class)) {
                    $text = __('naekb.vkbot::lang.commands.clean_time.error_500');
                } else {
                    $text = __('naekb.vkbot::lang.commands.clean_time.error');
                }
            }
        } else {
            $text = __('naekb.vkbot::lang.commands.clean_time.set_help');
        }

        $this->reply($text, [], false);
    }

    public function del(array $arguments = [])
    {
        $arguments['empathy'] = $arguments['empathy'] ?? 0;

        CleanDate::where('user_id', $this->userId)->delete();
        $this->reply($arguments['empathy'] ? __('naekb.vkbot::lang.commands.clean_time.empathy') : __('naekb.vkbot::lang.commands.clean_time.deleted'), [
            'inline'    => true,
            'buttons'   => [
                [
                    [
                        'action' => [
                            'type'      => 'callback',
                            'payload'   => json_encode([
                                'command'   => 'cleanTime',
                                'action'    => 'parse'
                            ], JSON_UNESCAPED_SLASHES),
                            'label'     => __('naekb.vkbot::lang.commands.clean_time.set_again')
                        ]
                    ]
                ]
            ]
        ], false);
    }

    /**
     * @throws VKApiException
     * @throws VKClientException
     */
    public function congr(?array $arguments = []) {
        $arguments['private'] = $arguments['private'] ?? 0;

        $today = Carbon::today();
        $months = CleanDate::selectRaw("TIMESTAMPDIFF(MONTH, `date` - INTERVAL 1 DAY, '{$today->toDateTimeString()}') as `clean`")
            ->where('user_id', $this->userId)->first();

        if (empty($months->clean)) {
            return;
        }

        $date = CleanDate::where('user_id', $this->userId)->first();
        $humanDate = $this->humanDate($date);

        if (!empty($date->last) && $date->last->isToday()) {
            $text = __('naekb.vkbot::lang.commands.clean_time.only_one');
            $this->reply($text, [], false);
            return;
        }

        if ($arguments['private']) {
            $text = __('naekb.vkbot::lang.commands.clean_time.congr', [
                'date' => $humanDate
            ]);
            $this->reply($text, [], false, false);
            $this->sendImage();
            return;
        } elseif($date->date->day == $today->day) {
            $userInfo = $this->callApi('users.get', [
                'user_ids' => $this->userId,
                'fields' => 'has_photo,photo_max_orig'
            ]);
            $avatarAccessible = !empty($userInfo[0]['has_photo'])
                && !empty($userInfo[0]['photo_max_orig'])
                && $this->isUrlAccessible($userInfo[0]['photo_max_orig']);

            $buttons = [
                [
                    'action' => [
                        'type' => 'callback',
                        'payload'  => json_encode([
                            'command' => 'cleanTime',
                            'action' => 'photo',
                        ], JSON_UNESCAPED_SLASHES),
                        'label' => __('naekb.vkbot::lang.commands.clean_time.photo')
                    ]
                ]
            ];

            if ($avatarAccessible) {
                $buttons[] = [
                    'action' => [
                        'type' => 'callback',
                        'payload' => json_encode([
                            'command' => 'cleanTime',
                            'action' => 'avatar',
                        ], JSON_UNESCAPED_SLASHES),
                        'label' => __('naekb.vkbot::lang.commands.clean_time.avatar')
                    ]
                ];
            }

            $buttons[] = [
                'action' => [
                    'type' => 'callback',
                    'payload' => json_encode([
                        'command' => 'cleanTime',
                        'action' => 'send',
                    ], JSON_UNESCAPED_SLASHES),
                    'label' => __('naekb.vkbot::lang.commands.clean_time.no_img')
                ]
            ];

            $buttons[] = [
                'action' => [
                    'type' => 'callback',
                    'payload' => json_encode([
                        'command' => 'cleanTime',
                        'action' => 'congrPriv',
                    ], JSON_UNESCAPED_SLASHES),
                    'label' => __('naekb.vkbot::lang.commands.clean_time.changed_mind')
                ]
            ];

            $keyboard = [
                'inline' => true,
                'buttons' => [$buttons]
            ];

            $this->reply(__('naekb.vkbot::lang.commands.clean_time.photo_question'), $keyboard, false, false);
            return;
        }

        $this->reply(__('naekb.vkbot::lang.commands.clean_time.only_today'), [], false, false);
        $this->sendImage();
    }

    public function photo()
    {
        $date = CleanDate::where('user_id', $this->userId)->first();

        if (!empty($date->last) && $date->last->isToday()) {
            $text = __('naekb.vkbot::lang.commands.clean_time.only_one');
            $this->reply($text, [], false);
            return;
        }

        // Кнопка отказа: если передумал загружать фото — возврат в меню выбора (Аватар/Без фото/Передумал)
        $cancelKeyboard = [
            'inline' => true,
            'buttons' => [[
                [
                    'action' => [
                        'type' => 'callback',
                        'payload' => json_encode(['command' => 'cleanTime', 'action' => 'congr'], JSON_UNESCAPED_SLASHES),
                        'label' => __('naekb.vkbot::lang.commands.clean_time.changed_mind')
                    ]
                ]
            ]]
        ];

        if (empty($this->object['message']->attachments)) {
            $this->reply(__('naekb.vkbot::lang.commands.clean_time.photo_request'), $cancelKeyboard, false, false);
            return;
        }

        $photo = false;
        foreach ($this->object['message']->attachments as $attachment) {
            if($attachment->type == 'photo') {
                $photo = $attachment->photo;
                break;
            }
        }

        if ($photo === false) {
            $this->reply(__('naekb.vkbot::lang.commands.clean_time.photo_non'), $cancelKeyboard, false, false);
            return;
        }

        try {
            $sizes = [
                's' => 1,
                'm' => 2,
                'x' => 3,
                'y' => 4,
                'z' => 5,
                'w' => 6
            ];
            $maxSize = [];
            foreach ($photo->sizes as $size) {
                if (!empty($sizes[$size->type]) && (empty($maxSize) || $sizes[$maxSize->type] < $sizes[$size->type])) {
                    $maxSize = $size;
                }
            }

            $photoModel = Photo::create([
                'original_url' => $maxSize->url
            ]);

            $attachment = "photo{$photo->owner_id}_{$photo->id}_{$photo->access_key}";

            $keyboard = [
                'inline' => true,
                'buttons' => [
                    [
                        [
                            'action' => [
                                'type' => 'callback',
                                'payload'  => json_encode([
                                    'command' => 'cleanTime',
                                    'action' => 'approve',
                                    'userId' => $this->userId,
                                    'photoId' => $photoModel->id
                                ], JSON_UNESCAPED_SLASHES),
                                'label' => __('naekb.vkbot::lang.notifications.post_confirm')
                            ],
                            'color' => 'positive'
                        ],
                        [
                            'action' => [
                                'type' => 'callback',
                                'payload' => json_encode([
                                    'command' => 'cleanTime',
                                    'action' => 'refuse',
                                    'userId' => $this->userId,
                                    'photo' => $attachment
                                ], JSON_UNESCAPED_SLASHES),
                                'label' => __('naekb.vkbot::lang.notifications.post_delete')
                            ],
                            'color' => 'negative'
                        ]
                    ]
                ]
            ];
            $this->sendToAdmins(__('naekb.vkbot::lang.notifications.photo_check'), $keyboard, [$attachment]);

            $humanDate = $this->humanDate($date);
            $text = __('naekb.vkbot::lang.commands.clean_time.photo_wait') . "\r\n" . "\r\n";
            $text .= __('naekb.vkbot::lang.commands.clean_time.congr', [
                'date' => $humanDate
            ]);
            $this->setCommandState('main', 'start');
            $this->reply($text, [], false, false);
            $this->sendImage();
            return;
        } catch (\Throwable $e) {
            report($e);
            $text = __('naekb.vkbot::lang.commands.clean_time.error_500');
            $this->setCommandState('main', 'start');
        }

        $this->reply($text, [], false, false);
    }

    public function approve()
    {
        if (!$this->isAdmin()) {
            return;
        }

        $arguments = [
            'fromAdmin' => true,
            'userId' => $this->payload['userId'],
            'photoId' => $this->payload['photoId']
        ];

        $this->send($arguments);
    }

    public function refuse()
    {
        if (!$this->isAdmin()) {
            return;
        }

        $date = CleanDate::where('user_id', $this->payload['userId'])->first();
        if (!empty($date->last) && $date->last->isToday()) {
            $text = __('naekb.vkbot::lang.notifications.already_pub');
            $this->reply($text, [], false, false);
            return;
        }

        $this->replyTo($this->payload['userId'], __('naekb.vkbot::lang.commands.clean_time.refused'), [], false);

        $text =  __('naekb.vkbot::lang.notifications.refused') . "\r\n" .
            __('naekb.vkbot::lang.notifications.admin', [
                'id' => $this->userId,
                'name' => $this->getUserName()
            ]);
        $this->sendToAdmins($text, [], [$this->payload['photo']]);

        CleanDate::where('user_id', $this->payload['userId'])->update([
            'last' => Carbon::now()
        ]);
    }

    public function avatar()
    {
        try {
            $date = CleanDate::where('user_id', $this->userId)->first();

            if (!empty($date->last) && $date->last->isToday()) {
                $text = __('naekb.vkbot::lang.commands.clean_time.only_one');
                $this->reply($text, [], false);
                return;
            }

            $user = $this->callApi('users.get', [
                'user_ids' => $this->userId,
                'fields' => 'has_photo,photo_max_orig,photo_id'
            ]);

            // Guard: авы нет / профиль закрыт / поле не пришло / URL недоступен
            $avatarUrl = $user[0]['photo_max_orig'] ?? null;
            if (empty($user[0]['has_photo'])
                || empty($avatarUrl)
                || !$this->isUrlAccessible($avatarUrl)
            ) {
                $this->setCommandState('main', 'start');
                $this->replyWithAvaFallback();
                return;
            }

            // Пытаемся получить полноразмерное фото профиля, иначе фолбэк на photo_max_orig (≤400px)
            $fullUrl = $this->resolveFullAvatarUrl($user[0]['photo_id'] ?? null, $avatarUrl);

            $photoModel = Photo::create([
                'original_url' => $fullUrl
            ]);

            if (empty($user[0]['photo_id'])) {
                $attachment = $this->saveAndSendPhoto($photoModel->id);
            } else {
                $attachment = "photo{$user[0]['photo_id']}";
            }

            $keyboard = [
                'inline' => true,
                'buttons' => [
                    [
                        [
                            'action' => [
                                'type' => 'callback',
                                'payload'  => json_encode([
                                    'command' => 'cleanTime',
                                    'action' => 'approve',
                                    'userId' => $this->userId,
                                    'photoId' => $photoModel->id
                                ], JSON_UNESCAPED_SLASHES),
                                'label' => __('naekb.vkbot::lang.notifications.post_confirm')
                            ],
                            'color' => 'positive'
                        ],
                        [
                            'action' => [
                                'type' => 'callback',
                                'payload' => json_encode([
                                    'command' => 'cleanTime',
                                    'action' => 'refuse',
                                    'userId' => $this->userId,
                                    'photo' => $attachment
                                ], JSON_UNESCAPED_SLASHES),
                                'label' => __('naekb.vkbot::lang.notifications.post_delete')
                            ],
                            'color' => 'negative'
                        ]
                    ]
                ]
            ];
            $this->sendToAdmins(__('naekb.vkbot::lang.notifications.photo_check'), $keyboard, [$attachment]);

            $humanDate = $this->humanDate($date);
            $text = __('naekb.vkbot::lang.commands.clean_time.ava_wait') . "\r\n" . "\r\n";
            $text .= __('naekb.vkbot::lang.commands.clean_time.congr', [
                'date' => $humanDate
            ]);
            $this->setCommandState('main', 'start');
            $this->reply($text, [], false, false);
            $this->sendImage();
            return;
        } catch (\Throwable $e) {
            report($e);
            $this->setCommandState('main', 'start');
            $this->replyWithAvaFallback();
            return;
        }
    }

    public function send(?array $arguments = [])
    {
        if (!empty($arguments['fromAdmin']) && !$this->isAdmin()) {
            return;
        }

        if (!empty($arguments['fromAdmin'])) {
            $date = CleanDate::where('user_id', $arguments['userId'])->first();
        } else {
            $date = CleanDate::where('user_id', $this->userId)->first();
        }


        if (!empty($date->last) && $date->last->isToday()) {
            $text = __('naekb.vkbot::lang.commands.clean_time.only_one');
            $this->reply($text, [], false, empty($arguments['fromAdmin']));
            return;
        }

        $date->update(['last' => Carbon::now()]);
        $humanDate = $this->humanDate($date);

        $rand = rand(0, count(__('naekb.vkbot::lang.commands.channel.congr')) - 1);
        if (!empty($arguments['fromAdmin'])) {
            $message = __("naekb.vkbot::lang.commands.channel.congr.{$rand}", [
                'userId'    => $arguments['userId'],
                'userName'  => $this->getUserName($arguments['userId']),
                'date'      => $humanDate
            ]);
        } else {
            $message = __("naekb.vkbot::lang.commands.channel.congr.{$rand}", [
                'userId'    => $this->userId,
                'userName'  => $this->getUserName(),
                'date'      => $humanDate
            ]);
        }

        $params = [
            'owner_id'      => VkSettings::get('group_id') * -1,
            'from_group'    => 1,
            'message'       => $message
        ];

        if (!empty($arguments['photoId'])) {
            try {
                $params['attachments'] = $this->saveAndSendPhoto($arguments['photoId']);
            } catch (\Throwable $e) {
                // Публикуем пост без фото, чтобы не терять поздравление
                report($e);
            }
        }

        $this->callApi('wall.post', $params, true);

        if (!empty($arguments['fromAdmin'])) {
            $text =  __('naekb.vkbot::lang.notifications.approved') . "\r\n" .
                __('naekb.vkbot::lang.notifications.admin', [
                    'id' => $this->userId,
                    'name' => $this->getUserName()
                ]);
            $this->sendToAdmins($text, [], array_filter([$params['attachments'] ?? null]));
            return;
        }

        $text = __('naekb.vkbot::lang.commands.clean_time.congr', [
            'date' => $humanDate
        ]);
        $this->setCommandState();

        $this->reply($text, [], false, false);
        $this->sendImage();
    }

    public function congrPriv()
    {
        $arguments['private'] = true;
        $this->congr($arguments);
    }

    public function empathy()
    {
        $arguments['empathy'] = 1;
        $this->del($arguments);
    }

    private function isUrlAccessible(string $url): bool
    {
        $headers = @get_headers($url);
        return $headers !== false && strpos($headers[0], '200') !== false;
    }

    /**
     * Возвращает URL максимального размера фотографии профиля по photo_id ("{owner_id}_{photo_id}").
     * Фолбэк на $fallbackUrl (photo_max_orig, ≤400px), если photo_id пуст, фото недоступно или произошла ошибка.
     */
    private function resolveFullAvatarUrl(?string $photoId, string $fallbackUrl): string
    {
        if (empty($photoId)) {
            return $fallbackUrl;
        }

        try {
            $result = $this->callApi('photos.getById', [
                'photos'      => $photoId,
                'photo_sizes' => 1,
            ]);
        } catch (\Throwable $e) {
            report($e);
            return $fallbackUrl;
        }

        if (empty($result[0]['sizes'])) {
            return $fallbackUrl;
        }

        // Приоритет типов размеров VK (как в photo()), от меньшего к большему: w≈2560px — максимум
        $priority = [
            's' => 1, 'm' => 2, 'x' => 3,
            'o' => 4, 'p' => 5, 'q' => 6, 'r' => 7,
            'y' => 8, 'z' => 9, 'w' => 10,
        ];
        $best = null;
        foreach ($result[0]['sizes'] as $size) {
            $type = $size['type'] ?? null;
            if (empty($priority[$type])) {
                continue;
            }
            if ($best === null || $priority[$best['type']] < $priority[$type]) {
                $best = $size;
            }
        }

        return $best['url'] ?? $fallbackUrl;
    }

    private function replyWithAvaFallback(): void
    {
        $keyboard = [
            'inline' => true,
            'buttons' => [[
                [
                    'action' => [
                        'type' => 'callback',
                        'payload' => json_encode(['command' => 'cleanTime', 'action' => 'photo'], JSON_UNESCAPED_SLASHES),
                        'label' => __('naekb.vkbot::lang.commands.clean_time.photo')
                    ]
                ],
                [
                    'action' => [
                        'type' => 'callback',
                        'payload' => json_encode(['command' => 'cleanTime', 'action' => 'send'], JSON_UNESCAPED_SLASHES),
                        'label' => __('naekb.vkbot::lang.commands.clean_time.no_img')
                    ]
                ],
                [
                    'action' => [
                        'type' => 'callback',
                        'payload' => json_encode(['command' => 'cleanTime', 'action' => 'congrPriv'], JSON_UNESCAPED_SLASHES),
                        'label' => __('naekb.vkbot::lang.commands.clean_time.changed_mind')
                    ]
                ]
            ]]
        ];
        $this->reply(__('naekb.vkbot::lang.commands.clean_time.ava_private'), $keyboard, false, false);
    }

    protected function saveAndSendPhoto(int $photoId): string
    {
        $photo = Photo::find($photoId);
        if (!empty($photo->attachment)) {
            return $photo->attachment;
        }

        if (empty($photo->original_url)) {
            throw new \RuntimeException("Photo #{$photoId}: original_url is empty");
        }

        // Имя временного файла обязано иметь валидное графическое расширение:
        // современные VK CDN-ссылки (impg/impf) не содержат его в пути (оно уходит
        // в query), из-за чего Guzzle отправляет файл как application/octet-stream,
        // VK отклоняет картинку и возвращает пустое photo => "photo is undefined".
        $urlPath = explode('?', $photo->original_url)[0];
        $ext = strtolower(pathinfo($urlPath, PATHINFO_EXTENSION));
        if (!in_array($ext, ['jpg', 'jpeg', 'png', 'gif', 'bmp', 'webp'], true)) {
            $ext = 'jpg';
        }
        $filename = "vk_photo_{$photoId}.{$ext}";

        $contents = @file_get_contents($photo->original_url);
        if ($contents === false || $contents === '') {
            throw new \RuntimeException("Photo #{$photoId}: failed to download {$photo->original_url}");
        }

        \Storage::disk('local')->put($filename, $contents);

        try {
            $server = $this->callApi('photos.getWallUploadServer', [
                'group_id' => VkSettings::get('group_id')
            ], true);
            $uploadedPhoto = $this->api->getRequest()->upload($server['upload_url'], 'photo', storage_path("app/{$filename}"));

            // VK при неудачной загрузке возвращает пустое photo (пустая строка или "[]"),
            // а photos.saveWallPhoto затем падает с "photo is undefined". Отсекаем заранее.
            if (empty($uploadedPhoto['photo']) || $uploadedPhoto['photo'] === '[]') {
                throw new \RuntimeException("Photo #{$photoId}: VK upload returned empty photo for {$photo->original_url}");
            }

            $savedPhotos = $this->callApi('photos.saveWallPhoto', [
                'group_id' => VkSettings::get('group_id'),
                'server' => $uploadedPhoto['server'],
                'photo' => $uploadedPhoto['photo'],
                'hash' => $uploadedPhoto['hash'],
            ], true);
        } finally {
            // Временный файл больше не нужен ни при успехе, ни при ошибке
            \Storage::disk('local')->delete($filename);
        }

        $photo->attachment = "photo{$savedPhotos[0]['owner_id']}_{$savedPhotos[0]['id']}_{$savedPhotos[0]['access_key']}";
        $photo->save();

        return $photo->attachment;
    }

    protected function sendImage(): void
    {
        $folderPath = plugins_path('naekb/vkbot/assets/img/');
        $imgFiles = [];
        foreach (new DirectoryIterator($folderPath) as $i => $fileInfo) {
            if ($fileInfo->isDot() || str_starts_with($fileInfo->getFilename(), '.')) continue;
            $imgFiles[] = $fileInfo->getFilename();
        }

        if (empty($imgFiles)) {
            \Log::warning('VK message image was not sent: image directory is empty');
            return;
        }

        shuffle($imgFiles);
        $attempts = min(3, count($imgFiles));
        $failures = [];

        for ($attempt = 0; $attempt < $attempts; $attempt++) {
            $image = $imgFiles[$attempt];
            $img = $folderPath . $image;

            try {
                // Для каждой попытки получаем новый upload URL и берём другую картинку.
                $server = $this->callApi('photos.getMessagesUploadServer', [
                    'peer_id' => $this->userId
                ]);

                if (empty($server['upload_url'])) {
                    $failures[$image] = 'upload_url_missing';
                    continue;
                }

                $uploadedPhoto = $this->api->getRequest()->upload($server['upload_url'], 'photo', $img);

                // При отклонённом VK файле upload endpoint может вернуть пустое photo.
                // Не вызываем photos.saveMessagesPhoto с невалидным параметром: иначе
                // VK отвечает VKApiParamException с сообщением "photo is undefined".
                if (empty($uploadedPhoto['server'])
                    || empty($uploadedPhoto['photo'])
                    || $uploadedPhoto['photo'] === '[]'
                    || empty($uploadedPhoto['hash'])
                ) {
                    $failures[$image] = 'upload_data_incomplete';
                    continue;
                }

                $savedPhotos = $this->callApi('photos.saveMessagesPhoto', [
                    'server' => $uploadedPhoto['server'],
                    'photo' => $uploadedPhoto['photo'],
                    'hash' => $uploadedPhoto['hash'],
                ]);

                if (empty($savedPhotos[0]['owner_id']) || empty($savedPhotos[0]['id'])) {
                    $failures[$image] = 'saved_photo_missing';
                    continue;
                }
            } catch (VKClientException $e) {
                // Транспортный сбой может быть временным — пробуем следующую картинку.
                $failures[$image] = 'transport_error';
                continue;
            } catch (VKApiException $e) {
                // Неизвестная/серверная ошибка, сбой загрузки и timeout могут быть временными.
                if (in_array($e->getErrorCode(), [1, 10, 22, 36], true)) {
                    $failures[$image] = "temporary_api_error_{$e->getErrorCode()}";
                    continue;
                }

                // Ошибки токена, прав и параметров повтором с другой картинкой не исправить.
                report($e);
                return;
            } catch (\Throwable $e) {
                report($e);
                return;
            }

            $attachment = "photo{$savedPhotos[0]['owner_id']}_{$savedPhotos[0]['id']}";
            if (!empty($savedPhotos[0]['access_key'])) {
                $attachment .= "_{$savedPhotos[0]['access_key']}";
            }

            try {
                $this->reply(__('naekb.vkbot::lang.commands.clean_time.image'), [], false, true, [$attachment]);
            } catch (\Throwable $e) {
                // Файл уже сохранён в VK: повторная загрузка здесь не поможет.
                report($e);
            }
            return;
        }

        \Log::warning('VK message image was not sent after retries', [
            'attempts' => $attempts,
            'failures' => $failures,
            'user_id' => $this->userId,
        ]);
    }

    protected function humanDate(?CleanDate $cleanDate) {
        if (empty($cleanDate)) {
            return null;
        }

        $from = $cleanDate->date;
        $diff = Carbon::now()->diff($from);
        $years = $diff->y;
        $months = $diff->m;
        $days = $diff->d;

        $date = '';
        if($years > 0) {
            if (app()->getLocale() == 'ru') {
                $date .= pluralize($years, 'год');
            } else {
                $date .= engPluralize($years, 'year');
            }
        }

        if($months > 0) {
            if (app()->getLocale() == 'ru') {
                $date .= ' ' . pluralize($months, 'месяц');
            } else {
                $date .= ' ' . engPluralize($months, 'month');
            }
        }

        if($days > 0) {
            if ($months > 0 || $years > 0) {
                $date .= ' ' . __('naekb.vkbot::lang.commands.and');
            }
            if (app()->getLocale() == 'ru') {
                $date .= ' ' . pluralize($days, 'день');
            } else {
                $date .= ' ' . engPluralize($days, 'day');
            }
        }

        return $date === '' ? null : $date;
    }
}
