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
        Schema::create('na_ekb_tg_page_relations', function(Blueprint $table) {
            $table->id();
            $table->foreignId('parent_id')
                ->constrained('na_ekb_tg_pages')
                ->onUpdate('cascade')
                ->onDelete('cascade');
            $table->foreignId('children_id')
                ->constrained('na_ekb_tg_pages')
                ->onUpdate('cascade')
                ->onDelete('cascade');

            $table->unique(['parent_id', 'children_id']);
        });
    }

    /**
     * down reverses the migration
     */
    public function down()
    {
        Schema::dropIfExists('na_ekb_tg_page_relations');
    }
};
