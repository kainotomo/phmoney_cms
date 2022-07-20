<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAccountsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::connection('phmoney_portfolio')->create('accounts', function (Blueprint $table) {
            $table->id('pk');
            $table->foreignIdFor(config('phmoney.foreign_id_model'), 'team_id')->index();
            $table->uuid('guid')->index();
            $table->string('name', 2048);
            $table->string('account_type', 2048);
            $table->uuid('commodity_guid')->nullable();
            $table->integer('commodity_scu');
            $table->integer('non_std_scu');
            $table->uuid('parent_guid')->nullable();
            $table->string('code', 2048)->nullable();
            $table->string('description', 2048)->nullable();
            $table->boolean('hidden')->nullable();
            $table->boolean('placeholder')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::connection('phmoney_portfolio')->dropIfExists('accounts');
    }
}
