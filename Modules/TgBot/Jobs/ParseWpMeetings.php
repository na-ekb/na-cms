<?php

namespace Modules\TgBot\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Http;

use Modules\TgBot\Entities\TgMeeting;

class ParseWpMeetings implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct()
    {

    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $url = "https://na-samara.com/wp-admin/admin-ajax.php?action=meetings";
        $response = Http::get($url);
        $response->collect()->each(function($group) {
            if (TgMeeting::where('slug', $group['slug'])->exists()) {
                TgMeeting::where('slug', $group['slug'])->first()->update($group);
            } else {
                (new TgMeeting)->fill($group)->save();
            }
        });
    }
}
