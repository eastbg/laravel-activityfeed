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
            $table->bigInteger('id_rule')->nullable()->unsigned();
            $table->tinyInteger('processed')->default(0);

            $table->string('dbtable')->nullable();
            $table->bigInteger('dbkey')->nullable()->unsigned();
            $table->string('operation')->nullable();
            $table->string('field')->nullable();

            $table->index([
                'processed'
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
