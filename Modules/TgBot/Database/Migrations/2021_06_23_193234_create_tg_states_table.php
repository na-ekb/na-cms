<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTgStatesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tg_states', function (Blueprint $table) {
            $table->id();
            $table->string('tg_user_id');
            $table->string('state');
            $table->string('prev')->nullable();
            $table->timestamps();

            $table->unique('tg_user_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('tg_states');
    }
}
