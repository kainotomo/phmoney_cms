<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSplitsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::connection('phmoney_portfolio')->create('splits', function (Blueprint $table) {
            $table->id('pk');
            $table->foreignIdFor(config('phmoney.foreign_id_model'), 'team_id')->index();
            $table->uuid('guid')->index();
            $table->uuid('tx_guid')->index('splits_tx_guid_index');
            $table->uuid('account_guid')->index('splits_account_guid_index');
            $table->string('memo', 2048);
            $table->string('action', 2048);
            $table->string('reconcile_state', 1);
            $table->dateTime('reconcile_date')->default('1970-01-01 00:00:00');
            $table->bigInteger('value_num');
            $table->bigInteger('value_denom');
            $table->bigInteger('quantity_num');
            $table->bigInteger('quantity_denom');
            $table->uuid('lot_guid')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::connection('phmoney_portfolio')->dropIfExists('splits');
    }
}
