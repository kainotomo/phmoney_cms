<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOrdersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::connection('phmoney_portfolio')->create('orders', function (Blueprint $table) {
            $table->id('pk');
            $table->foreignIdFor(config('phmoney.foreign_id_model'), 'team_id')->index();
            $table->uuid('guid')->index();
            $table->string('id', 2048);
            $table->string('notes', 2048);
            $table->string('reference', 2048);
            $table->boolean('active');
            $table->dateTime('date_opened')->default('1970-01-01 00:00:00');
            $table->dateTime('date_closed')->default('1970-01-01 00:00:00');
            $table->integer('owner_type');
            $table->uuid('owner_guid');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::connection('phmoney_portfolio')->dropIfExists('orders');
    }
}
