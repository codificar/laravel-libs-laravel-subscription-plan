<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateProviderTableSignature extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('provider', function (Blueprint $table) {
            if (!Schema::hasColumn('provider', 'signature_id')) {
                $table->integer('signature_id')->nullable();
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
        Schema::table('provider', function (Blueprint $table) {
            if (Schema::hasColumn('provider', 'signature_id')) {
                $table->integer('signature_id');
            }
        });
    }
}