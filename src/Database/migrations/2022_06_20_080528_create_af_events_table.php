<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAfEventsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('af_events', function (Blueprint $table) {
            $table->id();
            $table->timestamps();

            $table->bigInteger('id_user_creator')->nullable()->unsigned();
            $table->bigInteger('id_template')->nullable()->unsigned();
            $table->bigInteger('id_rule')->nullable()->unsigned();
            $table->bigInteger('id_category')->nullable()->unsigned();

            $table->json('targeting');
            $table->dateTime('expiry');

            $table->tinyInteger('processed')->default(0);
            $table->tinyInteger('admins')->default(0);
            $table->tinyInteger('digest')->default(0);
            $table->tinyInteger('digested')->default(0);
            $table->tinyInteger('to_admins')->default(0);
            $table->tinyInteger('background_job')->default(0);
            $table->tinyInteger('popup')->default(0);

            $table->index([
                'expiry','processed','admins','digest','digested'
            ]);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('af_events');
    }
}
