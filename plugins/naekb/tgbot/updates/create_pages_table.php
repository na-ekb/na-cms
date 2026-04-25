<?php namespace NaEkb\TgBot\Updates;

use Schema;
use October\Rain\Database\Schema\Blueprint;
use October\Rain\Database\Updates\Migration;

/**
 * CreatePagesTable Migration
 *
 * @link https://docs.octobercms.com/3.x/extend/database/structure.html
 */
return new class extends Migration
{
    /**
     * up builds the migration
     */
    public function up()
    {
        Schema::create('na_ekb_tg_pages', function(Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('site_root_id')->nullable();
            $table->unsignedBigInteger('site_id')->nullable();
            $table->string('title');
            $table->text('content');
            $table->text('note')->nullable();
            $table->boolean('active')->default(0);
            $table->boolean('inside')->default(0);
            $table->string('command');
            $table->unsignedSmallInteger('order')->default(1);
        });
    }

    /**
     * down reverses the migration
     */
    public function down()
    {
        Schema::dropIfExists('na_ekb_tg_pages');
    }
};
