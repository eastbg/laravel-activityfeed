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
            $table->bigInteger('id_master_template')->nullable()->unsigned();
            $table->bigInteger('id_user_owner')->nullable()->unsigned();

            $table->string('rule_script')->nullable();
            $table->string('creator_script')->nullable();

            $table->json('targeting')->nullable();
            $table->json('channels')->nullable();

            $table->string('name')->nullable();
            $table->text('description')->nullable();

            $table->string('rule_type')->nullable();
            $table->string('rule')->nullable();

            $table->string('table_name')->nullable();
            $table->string('field_name')->nullable();

            $table->string('rule_operator')->nullable();
            $table->string('rule_value')->nullable();

            $table->string('rule_actions')->nullable();
            $table->string('context')->nullable();

            $table->tinyInteger('to_admins')->default(0);
            $table->tinyInteger('background_job')->default(0);
            $table->tinyInteger('digestible')->default(0);
            $table->tinyInteger('enabled')->default(0);
            $table->tinyInteger('popup')->default(0);

            $table->index([
                'to_admins','background_job','digestible','enabled'
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
        Schema::dropIfExists('af_rules');
    }
}
