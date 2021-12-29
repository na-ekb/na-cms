<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

use App\Enums\MeetingsType;

class AddMeetingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('meetings', function (Blueprint $table) {
            $table->id();
            $table->json('title');
            $table->json('city');
            $table->json('location');
            $table->unsignedSmallInteger('type');
            $table->json('address')->nullable();
            $table->json('address_description')->nullable();
            $table->string('lat')->nullable();
            $table->string('long')->nullable();
            $table->string('link')->nullable();
            $table->json('link_text')->nullable();
            $table->json('online')->nullable();
            $table->string('password')->nullable();
            $table->json('description')->nullable();
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
        Schema::dropIfExists('meetings');
    }
}
