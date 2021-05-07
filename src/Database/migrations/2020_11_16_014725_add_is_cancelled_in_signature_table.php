<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddIsCancelledInSignatureTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('signature', function (Blueprint $table) {
            if (!Schema::hasColumn('signature', 'is_cancelled')) {
                $table->boolean('is_cancelled')
                    ->nullable()
                    ->default(false);
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
            if (Schema::hasColumn('signature', 'is_cancelled')) {
                $table->dropColumn('is_cancelled');
            }
        });
    }
}
