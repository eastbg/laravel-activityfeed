<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAfTemplatesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('af_templates', function (Blueprint $table) {
            $table->id();
            $table->timestamps();

            $table->bigInteger('id_category')->nullable()->unsigned();

            $table->string('name')->nullable();
            $table->text('description')->nullable();

            $table->string('notification_subject')->nullable();
            $table->text('notification_template')->nullable();

            $table->string('email_subject')->nullable();
            $table->text('email_template')->nullable();

            $table->string('digest_subject')->nullable();
            $table->text('digest_template')->nullable();

            $table->string('admin_subject')->nullable();
            $table->text('admin_template')->nullable();

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
        Schema::dropIfExists('af_templates');
    }
}
