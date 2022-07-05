<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AfNotifications extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('af_notifications', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->bigInteger('id_user_recipient')->nullable()->unsigned();
            $table->bigInteger('id_user_creator')->nullable()->unsigned();
            $table->bigInteger('id_rule')->nullable()->unsigned();
            $table->bigInteger('id_event')->nullable()->unsigned();
            $table->dateTime('expiry')->nullable()->default(null);
            $table->tinyInteger('sent')->default(0);
            $table->tinyInteger('read')->default(0);
            $table->tinyInteger('digestible')->default(0);
            $table->tinyInteger('digested')->default(0);
            $table->tinyInteger('processed')->default(0);
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
