<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateCreateSignatureTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //
        if (!Schema::hasTable('signature')) {
            //
            Schema::create('signature', function (Blueprint $table) {
                //
                $table->increments('id');
                $table->integer('provider_id')->nullable();
                $table->integer('plan_id')->nullable();
                $table->integer('finance_id')->nullable();
                $table->tinyInteger('activity')->nullable();
                $table->date('next_expiration')->nullable();
                $table->timestamps();
            });
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
        Schema::dropIfExists('signature');
    }
}