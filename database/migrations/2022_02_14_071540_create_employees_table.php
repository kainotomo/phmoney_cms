<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEmployeesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::connection('phmoney_portfolio')->create('employees', function (Blueprint $table) {
            $table->id('pk');
            $table->foreignIdFor(config('phmoney.foreign_id_model'), 'team_id')->index();
            $table->uuid('guid')->index();
            $table->string('username', 2048);
            $table->string('id', 2048);
            $table->string('language', 2048);
            $table->string('acl', 2048);
            $table->boolean('active');
            $table->uuid('currency');
            $table->uuid('ccard_guid')->nullable();
            $table->bigInteger('workday_num');
            $table->bigInteger('workday_denom');
            $table->bigInteger('rate_num');
            $table->bigInteger('rate_denom');
            $table->string('addr_name', 1024)->nullable();
            $table->string('addr_addr1', 1024)->nullable();
            $table->string('addr_addr2', 1024)->nullable();
            $table->string('addr_addr3', 1024)->nullable();
            $table->string('addr_addr4', 1024)->nullable();
            $table->string('addr_phone', 128)->nullable();
            $table->string('addr_fax', 128)->nullable();
            $table->string('addr_email', 256)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::connection('phmoney_portfolio')->dropIfExists('employees');
    }
}
