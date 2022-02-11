<?php

namespace Modules\TgBot\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Console\Scheduling\Schedule;

use Modules\TgBot\Jobs\SendMeetingsToChannel;
use Modules\TgBot\Jobs\ParseWpMeetings;
use Modules\TgBot\Jobs\CleanTime;

class ScheduleServiceProvider extends ServiceProvider
{
    public function boot()
    {
        $this->app->booted(function () {
            $schedule = $this->app->make(Schedule::class);
            $schedule->job(new ParseWpMeetings)->everyMinute();
            $schedule->job(new SendMeetingsToChannel)->dailyAt('08:00');
            $schedule->job(new CleanTime)->dailyAt(config('TgBot.tg_cleanTimeAt', '09:00'));
        });
    }

    public function register()
    {
    }
}