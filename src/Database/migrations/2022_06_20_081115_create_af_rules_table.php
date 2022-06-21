<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAfRulesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('af_rules', function (Blueprint $table) {
            $table->id();
            $table->timestamps();

            $table->bigInteger('id_category')->nullable()->unsigned();
            $table->bigInteger('id_template')->nullable()->unsigned();

            $table->string('name');
            $table->text('description');

            $table->string('rule_type');
            $table->string('rule');

            $table->string('table_name');
            $table->string('field_name');

            $table->string('rule_operator');
            $table->string('rule_value');

            $table->string('rule_actions');
            $table->string('context');

            $table->tinyInteger('background_job')->default(0);
            $table->tinyInteger('digestible')->default(0);
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
        Schema::dropIfExists('af_rules');
    }
}
