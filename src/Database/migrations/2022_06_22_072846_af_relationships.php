<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AfRelationships extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('af_events', function (Blueprint $table) {
            $table->foreign('id_user_creator')->references('id')->on('users')->onDelete('SET NULL');
            $table->foreign('id_rule')->references('id')->on('af_rules')->onDelete('CASCADE');
        });

        Schema::table('af_templates', function (Blueprint $table) {
            $table->foreign('id_category')->references('id')->on('af_categories')->onDelete('SET NULL');
        });

        Schema::table('af_rules', function (Blueprint $table) {
            $table->foreign('id_category')->references('id')->on('af_categories')->onDelete('SET NULL');
            $table->foreign('id_template')->references('id')->on('af_templates')->onDelete('SET NULL');
        });

        Schema::table('af_notifications', function (Blueprint $table) {
            $table->foreign('id_user_recipient')->references('id')->on('users')->onDelete('CASCADE');
            $table->foreign('id_user_creator')->references('id')->on('users')->onDelete('SET NULL');
            $table->foreign('id_rule')->references('id')->on('af_rules')->onDelete('CASCADE');
        });


    }


    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
}
