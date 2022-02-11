<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTgPagesRelationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tg_page_relations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('parent_id')
                ->references('id')
                ->on('tg_pages')
                ->onUpdate('cascade')
                ->onDelete('cascade');
            $table->foreignId('children_id')
                ->references('id')
                ->on('tg_pages')
                ->onUpdate('cascade')
                ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('tg_page_relations');
    }
}
