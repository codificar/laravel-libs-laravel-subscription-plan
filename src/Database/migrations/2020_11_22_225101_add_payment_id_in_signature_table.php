<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddPaymentIdInSignatureTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('signature', function (Blueprint $table) {
            if (!Schema::hasColumn('signature', 'payment_id')) {

                $table->integer('payment_id')->nullable()->unsigned();
                $table->foreign('payment_id')->references('id')->on('payment');
            }
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('signature', function (Blueprint $table) {
            if (Schema::hasColumn('signature', 'payment_id')) {

                $table->dropForeign('signature_payment_id_foreign');
                $table->dropColumn('payment_id');
            }
        });
    }
}
