<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddMeetingDaysFormatsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('meeting_days_formats', function (Blueprint $table) {
            $table->foreignId('meeting_day_id');
            $table->foreignId('meeting_day_format_id');

            $table->unique(['meeting_day_id', 'meeting_day_format_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('meeting_days_formats');
    }
}
