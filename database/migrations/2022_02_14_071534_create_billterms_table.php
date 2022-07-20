<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBilltermsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::connection('phmoney_portfolio')->create('billterms', function (Blueprint $table) {
            $table->id('pk');
            $table->foreignIdFor(config('phmoney.foreign_id_model'), 'team_id')->index();
            $table->uuid('guid')->index();
            $table->string('name', 2048);
            $table->string('description', 2048);
            $table->integer('refcount');
            $table->boolean('invisible');
            $table->uuid('parent')->nullable();
            $table->string('type', 2048);
            $table->integer('duedays')->nullable();
            $table->integer('discountdays')->nullable();
            $table->bigInteger('discount_num')->nullable();
            $table->bigInteger('discount_denom')->nullable();
            $table->integer('cutoff')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::connection('phmoney_portfolio')->dropIfExists('billterms');
    }
}
