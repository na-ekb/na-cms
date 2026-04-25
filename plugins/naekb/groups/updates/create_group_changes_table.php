<?php

use October\Rain\Database\Schema\Blueprint;
use October\Rain\Database\Updates\Migration;

/**
 * Migration for GroupEntry Repeater Table
 */
return new class extends Migration
{
    public function up()
    {
        Schema::create('naekb_group_changes', function(Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('group_id')->index();
            $table->unsignedTinyInteger('online');
            $table->unsignedTinyInteger('type');
            $table->unsignedTinyInteger('day');
            $table->string('time');
            $table->unsignedTinyInteger('format');
            $table->string('format_second')->nullable();
            $table->softDeletes();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('naekb_group_changes');
    }
};
