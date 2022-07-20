<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSlotsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::connection('phmoney_portfolio')->create('slots', function (Blueprint $table) {
            $table->id('pk');
            $table->bigInteger('id');
            $table->foreignIdFor(config('phmoney.foreign_id_model'), 'team_id')->index();
            $table->uuid('guid')->nullable();
            $table->uuid('obj_guid')->index('slots_guid_index');
            $table->string('name', 4096);
            $table->integer('slot_type');
            $table->bigInteger('int64_val')->nullable();
            $table->string('string_val', 4096)->nullable();
            $table->double('double_val')->nullable();
            $table->dateTime('timespec_val')->default('1970-01-01 00:00:00');
            $table->uuid('guid_val')->nullable();
            $table->bigInteger('numeric_val_num')->nullable();
            $table->bigInteger('numeric_val_denom')->nullable();
            $table->date('gdate_val')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::connection('phmoney_portfolio')->dropIfExists('slots');
    }
}
