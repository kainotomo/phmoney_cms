<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEntriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::connection('phmoney_portfolio')->create('entries', function (Blueprint $table) {
            $table->id('pk');
            $table->foreignIdFor(config('phmoney.foreign_id_model'), 'team_id')->index();
            $table->uuid('guid')->index();
            $table->dateTime('date')->default('1970-01-01 00:00:00');
            $table->dateTime('date_entered')->default('1970-01-01 00:00:00');
            $table->string('description', 2048)->nullable();
            $table->string('action', 2048)->nullable();
            $table->string('notes', 2048)->nullable();
            $table->bigInteger('quantity_num')->nullable();
            $table->bigInteger('quantity_denom')->nullable();
            $table->uuid('i_acct')->nullable();
            $table->bigInteger('i_price_num')->nullable();
            $table->bigInteger('i_price_denom')->nullable();
            $table->bigInteger('i_discount_num')->nullable();
            $table->bigInteger('i_discount_denom')->nullable();
            $table->uuid('invoice')->nullable();
            $table->string('i_disc_type', 2048)->nullable();
            $table->string('i_disc_how', 2048)->nullable();
            $table->boolean('i_taxable')->nullable();
            $table->boolean('i_taxincluded')->nullable();
            $table->uuid('i_taxtable')->nullable();
            $table->uuid('b_acct')->nullable();
            $table->bigInteger('b_price_num')->nullable();
            $table->bigInteger('b_price_denom')->nullable();
            $table->uuid('bill')->nullable();
            $table->boolean('b_taxable')->nullable();
            $table->boolean('b_taxincluded')->nullable();
            $table->uuid('b_taxtable')->nullable();
            $table->integer('b_paytype')->nullable();
            $table->boolean('billable')->nullable();
            $table->integer('billto_type')->nullable();
            $table->uuid('billto_guid')->nullable();
            $table->uuid('order_guid')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::connection('phmoney_portfolio')->dropIfExists('entries');
    }
}
