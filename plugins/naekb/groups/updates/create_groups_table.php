<?php

use October\Rain\Database\Schema\Blueprint;
use October\Rain\Database\Updates\Migration;

/**
 * Migration for GroupEntry
 */
return new class extends Migration
{
    public function up()
    {
        Schema::create('naekb_groups', function(Blueprint $table) {
            $table->id();
            $table->integer('site_id')->nullable()->index();
            $table->integer('site_root_id')->nullable()->index();
            $table->string('title')->nullable();
            $table->string('slug')->nullable()->index();
            $table->text('type')->nullable();
            $table->text('address_help')->nullable();
            $table->text('city')->nullable();
            $table->text('location')->nullable();
            $table->text('address')->nullable();
            $table->text('address_desc')->nullable();
            $table->text('lat')->nullable();
            $table->text('lon')->nullable();
            $table->text('link')->nullable();
            $table->text('link_text')->nullable();
            $table->text('password')->nullable();
            $table->mediumText('description')->nullable();
            $table->softDeletes();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('naekb_groups');
    }
};
