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

            $table->bigInteger('id_template')->nullable()->unsigned();
            $table->bigInteger('id_rule')->nullable()->unsigned();
            $table->bigInteger('id_category')->nullable()->unsigned();

            $table->json('channels');

            $table->string('notification_subject');
            $table->text('notification_template');

            $table->string('email_subject');
            $table->text('email_template');

            $table->string('digest_subject');
            $table->text('digest_template');

            $table->string('admin_subject');
            $table->text('admin_template');

            $table->dateTime('expiry');

            $table->tinyInteger('sent')->default(0);
            $table->tinyInteger('read')->default(0);
            $table->tinyInteger('digest')->default(0);
            $table->tinyInteger('digested')->default(0);

            $table->tinyInteger('processed')->default(0);
            $table->tinyInteger('popup')->default(0);

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
