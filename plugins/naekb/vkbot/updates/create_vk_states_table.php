<?php

use Illuminate\Support\Facades\Schema;
use October\Rain\Database\Schema\Blueprint;
use October\Rain\Database\Updates\Migration;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('na_ekb_vk_states', function (Blueprint $table) {
            $table->id();
            $table->string('user_id');
            $table->string('state')->default('start');
            $table->string('action')->default('main');
            $table->string('prev')->nullable();
            $table->string('prev_action')->nullable();
            $table->boolean('banned')->default(0);
            $table->boolean('allow')->default(1);
            $table->timestamps();

            $table->unique('user_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('na_ekb_vk_states');
    }
};
