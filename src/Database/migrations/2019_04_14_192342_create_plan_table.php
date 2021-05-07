<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePlanTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('plan', function (Blueprint $table) {
            //
            $table->increments('id');
            $table->integer('signature_id')->nullable();
            $table->string('name');
            $table->integer('period');
            $table->double('plan_price');
            $table->enum('client', ['Provider', 'User']);
            $table->integer('validity')->Nullable(false);
            $table->integer('visibility')->default(1);
            $table->integer('location')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('plan');
    }
}