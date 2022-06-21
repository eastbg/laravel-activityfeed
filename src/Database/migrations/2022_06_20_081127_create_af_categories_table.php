<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAfCategoriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('af_categories', function (Blueprint $table) {
            $table->id();
            $table->timestamps();

            $table->string('name');
            $table->text('description');
            $table->string('ui_placement');

            $table->tinyInteger('enabled')->default(0);
        });

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('af_categories');
    }
}
