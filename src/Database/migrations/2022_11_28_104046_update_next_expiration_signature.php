<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateNextExpirationSignature extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('signature', function (Blueprint $table) {
            if (Schema::hasColumn('signature', 'next_expiration')) {
                $table->dateTime('next_expiration')->useCurrent()->nullable()->change();
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
            if (Schema::hasColumn('signature', 'next_expiration')) {
                $table->date('next_expiration')->nullable()->change();
            }
        });
    }
}
