<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCustomersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::connection('phmoney_portfolio')->create('customers', function (Blueprint $table) {
            $table->id('pk');
            $table->foreignIdFor(config('phmoney.foreign_id_model'), 'team_id')->index();
            $table->uuid('guid')->index();
            $table->string('name', 2048);
            $table->string('id', 2048);
            $table->string('notes', 2048);
            $table->boolean('active');
            $table->bigInteger('discount_num');
            $table->bigInteger('discount_denom');
            $table->bigInteger('credit_num');
            $table->bigInteger('credit_denom');
            $table->uuid('currency');
            $table->boolean('tax_override');
            $table->string('addr_name', 1024)->nullable();
            $table->string('addr_addr1', 1024)->nullable();
            $table->string('addr_addr2', 1024)->nullable();
            $table->string('addr_addr3', 1024)->nullable();
            $table->string('addr_addr4', 1024)->nullable();
            $table->string('addr_phone', 128)->nullable();
            $table->string('addr_fax', 128)->nullable();
            $table->string('addr_email', 256)->nullable();
            $table->string('shipaddr_name', 1024)->nullable();
            $table->string('shipaddr_addr1', 1024)->nullable();
            $table->string('shipaddr_addr2', 1024)->nullable();
            $table->string('shipaddr_addr3')->nullable();
            $table->string('shipaddr_addr4')->nullable();
            $table->string('shipaddr_phone', 128)->nullable();
            $table->string('shipaddr_fax', 128)->nullable();
            $table->string('shipaddr_email', 256)->nullable();
            $table->uuid('terms')->nullable();
            $table->boolean('tax_included')->nullable();
            $table->uuid('taxtable')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::connection('phmoney_portfolio')->dropIfExists('customers');
    }
}
