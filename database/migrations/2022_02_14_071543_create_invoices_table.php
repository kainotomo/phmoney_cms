<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateInvoicesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::connection('phmoney_portfolio')->create('invoices', function (Blueprint $table) {
            $table->id('pk');
            $table->foreignIdFor(config('phmoney.foreign_id_model'), 'team_id')->index();
            $table->uuid('guid')->index();
            $table->string('id', 2048);
            $table->dateTime('date_opened')->default('1970-01-01 00:00:00');
            $table->dateTime('date_posted')->nullable();
            $table->string('notes', 2048);
            $table->boolean('active');
            $table->uuid('currency');
            $table->integer('owner_type')->nullable();
            $table->uuid('owner_guid')->nullable();
            $table->uuid('terms')->nullable();
            $table->string('billing_id', 2048)->nullable();
            $table->uuid('post_txn')->nullable();
            $table->uuid('post_lot')->nullable();
            $table->uuid('post_acc')->nullable();
            $table->integer('billto_type')->nullable();
            $table->uuid('billto_guid')->nullable();
            $table->bigInteger('charge_amt_num')->nullable();
            $table->bigInteger('charge_amt_denom')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::connection('phmoney_portfolio')->dropIfExists('invoices');
    }
}
