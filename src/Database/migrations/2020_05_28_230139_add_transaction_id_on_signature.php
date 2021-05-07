<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddTransactionIdOnSignature extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasColumn('signature', 'transaction_id')) {
            Schema::table('signature', function (Blueprint $table) {
                $table->string('transaction_id')->nullable()->after('activity');
                $table->string('charge_type')->nullable();
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
        if (Schema::hasColumn('signature', 'transaction_id')) {
            Schema::table('signature', function (Blueprint $table) {
                $table->dropColumn('transaction_id');
                $table->dropColumn('charge_type');
            });
        }
    }
}
