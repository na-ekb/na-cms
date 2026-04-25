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
        Schema::create('na_ekb_vk_clean_dates', function (Blueprint $table) {
            $table->id();
            $table->string('user_id');
            $table->timestamp('date')->nullable();
            $table->timestamp('last')->nullable();
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
        Schema::dropIfExists('na_ekb_vk_clean_dates');
    }
};
