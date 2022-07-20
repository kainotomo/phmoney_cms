<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBudgetsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::connection('phmoney_portfolio')->create('budgets', function (Blueprint $table) {
            $table->id('pk');
            $table->foreignIdFor(config('phmoney.foreign_id_model'), 'team_id')->index();
            $table->uuid('guid')->index();
            $table->string('name', 2048);
            $table->string('description', 2048)->nullable();
            $table->integer('num_periods');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::connection('phmoney_portfolio')->dropIfExists('budgets');
    }
}
