<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSchedxactionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::connection('phmoney_portfolio')->create('schedxactions', function (Blueprint $table) {
            $table->id('pk');
            $table->foreignIdFor(config('phmoney.foreign_id_model'), 'team_id')->index();
            $table->uuid('guid')->index();
            $table->string('name', 2048)->nullable();
            $table->boolean('enabled');
            $table->date('start_date')->nullable();
            $table->date('end_date')->nullable();
            $table->date('last_occur')->nullable();
            $table->integer('num_occur');
            $table->integer('rem_occur');
            $table->integer('auto_create');
            $table->integer('auto_notify');
            $table->integer('adv_creation');
            $table->integer('adv_notify');
            $table->integer('instance_count');
            $table->uuid('template_act_guid');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::connection('phmoney_portfolio')->dropIfExists('schedxactions');
    }
}
