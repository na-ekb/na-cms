<?php namespace NAEkb\Pages\Updates;

use Schema;
use October\Rain\Database\Schema\Blueprint;
use October\Rain\Database\Updates\Migration;

class ChangesTable extends Migration
{
    /**
     * up builds the migration
     */
    public function up()
    {
        Schema::create('na_ekb_groups', function(Blueprint $table) {
            $table->id();
            $table->string('city');
            $table->string('location');
            $table->string('address');
            $table->text('address_desc');
            $table->decimal('lat', 11, 7);
            $table->decimal('lon', 11, 7);
            $table->string('link');
            $table->string('link_text');
            $table->string('password');
            $table->text('description');
            $table->boolean('not_active');
            $table->timestamps();
        });
    }

    /**
     * down reverses the migration
     */
    public function down()
    {
        Schema::dropIfExists('na_ekb_groups');
    }
};
