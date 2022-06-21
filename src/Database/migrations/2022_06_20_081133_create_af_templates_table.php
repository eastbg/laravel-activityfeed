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

            $table->string('name');
            $table->text('description');

            $table->string('notification_subject');
            $table->text('notification_template');

            $table->string('email_subject');
            $table->text('email_template');

            $table->string('digest_subject');
            $table->text('digest_template');

            $table->string('admin_subject');
            $table->text('admin_template');

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
