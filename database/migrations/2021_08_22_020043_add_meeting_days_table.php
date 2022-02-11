<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

use App\Enums\MeetingDayWeekdaysType;

class AddMeetingDaysTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('meeting_days', function (Blueprint $table) {
            $table->id();
            $table->foreignId('meeting_id');
            $table->string('type');
            $table->unsignedSmallInteger('format');
            $table->json('format_second')->nullable();
            $table->unsignedTinyInteger('online')->nullable();
            $table->unsignedTinyInteger('day_type')->nullable();
            $table->enum('day', [1, 2, 3, 4, 5, 6, 0]);
            $table->timestamp('date')->nullable();
            $table->timeTz('time');
            $table->unsignedInteger('duration');
            $table->timestamps();

            $table->index('meeting_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('meeting_days');
    }
}
