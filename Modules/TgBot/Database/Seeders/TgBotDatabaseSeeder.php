<?php

namespace Modules\TgBot\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;

use App\Models\Setting;

class TgBotDatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Model::unguard();

        // URL fro group parse
        Setting::create([
            'module'    => 'TgBot',
            'key'       => 'tg_parse_url'
        ]);

        // Type of parsed feed
        Setting::create([
            'module'    => 'TgBot',
            'key'       => 'tg_parse_type'
        ]);

        // Bot api token
        Setting::create([
            'module'    => 'TgBot',
            'key'       => 'tg_api_key'
        ]);

        // Telegram channel if exists
        Setting::create([
            'module'    => 'TgBot',
            'key'       => 'tg_channel'
        ]);

        // Is channel admins be admins in bot
        Setting::create([
            'module'    => 'TgBot',
            'key'       => 'tg_channel_admins'
        ]);

        // Secret key for webhook
        Setting::create([
            'module'    => 'TgBot',
            'key'       => 'tg_webhook_token',
        ]);
    }
}
