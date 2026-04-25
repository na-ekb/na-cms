<?php namespace NaEkb\TgBot\Updates;

use Schema;
use October\Rain\Database\Schema\Blueprint;
use October\Rain\Database\Updates\Migration;

return new class extends Migration
{
    /**
     * up builds the migration
     */
    public function up()
    {
        Schema::create('na_ekb_tg_page_links', function(Blueprint $table) {
            $table->id();
            $table->foreignId('page_id')
                ->constrained('na_ekb_tg_pages')
                ->onUpdate('cascade')
                ->onDelete('cascade');
            $table->string('text');
            $table->string('url');
            $table->string('data')->nullable();
            $table->boolean('inside')->default(0);
            $table->unsignedSmallInteger('order')->default(1);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * down reverses the migration
     */
    public function down()
    {
        Schema::dropIfExists('na_ekb_tg_page_links');
    }
};
