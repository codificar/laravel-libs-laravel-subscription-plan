<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddCheckBilletAtInSignatureTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('signature', function (Blueprint $table) {
            if (!Schema::hasColumn('signature', 'check_billet_at')) {

                $table->date('check_billet_at')->nullable();
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
            if (Schema::hasColumn('signature', 'check_billet_at')) {

                $table->dropColumn('check_billet_at');
            }
        });
    }
}
