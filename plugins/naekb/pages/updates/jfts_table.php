<?php namespace NAEkb\Pages\Updates;

use Schema;
use October\Rain\Database\Schema\Blueprint;
use October\Rain\Database\Updates\Migration;

class JftsTable extends Migration
{
    /**
     * up builds the migration
     */
    public function up()
    {
        Schema::create('na_ekb_jfts', function(Blueprint $table) {
            $table->id();
            $table->string('header');
            $table->text('quote');
            $table->string('from');
            $table->timestamps();
        });
    }

    /**
     * down reverses the migration
     */
    public function down()
    {
        Schema::dropIfExists('na_ekb_jfts');
    }
};
