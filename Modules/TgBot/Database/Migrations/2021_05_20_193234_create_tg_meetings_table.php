<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTgMeetingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tg_meetings', function (Blueprint $table) {
            $table->id();
            $table->string('name')->nullable();
            $table->string('slug');
            $table->string('notes')->nullable();
            $table->string('url');
            $table->unsignedTinyInteger('day')->nullable();
            $table->time('time');
            $table->time('end_time');
            $table->string('time_formatted');
            $table->string('conference_url')->nullable();
            $table->string('conference_url_notes')->nullable();
            $table->string('conference_phone')->nullable();
            $table->string('conference_phone_notes')->nullable();
            $table->string('location')->nullable();
            $table->string('location_notes')->nullable();
            $table->string('location_url')->nullable();
            $table->string('formatted_address');
            $table->decimal('latitude', 12, 8);
            $table->decimal('longitude', 12, 8);
            $table->string('region');
            $table->string('group')->nullable();
            $table->string('group_notes')->nullable();
            $table->string('website')->nullable();
            $table->string('website_2')->nullable();
            $table->string('email')->nullable();
            $table->string('phone')->nullable();
            $table->string('mailing_address')->nullable();
            $table->string('venmo')->nullable();
            $table->string('square')->nullable();
            $table->string('paypal')->nullable();
            $table->string('contact_1_name')->nullable();
            $table->string('contact_1_email')->nullable();
            $table->string('contact_1_phone')->nullable();
            $table->string('contact_2_name')->nullable();
            $table->string('contact_2_email')->nullable();
            $table->string('contact_2_phone')->nullable();
            $table->string('contact_3_name')->nullable();
            $table->string('contact_3_email')->nullable();
            $table->string('contact_3_phone')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('tg_meetings');
    }
}
