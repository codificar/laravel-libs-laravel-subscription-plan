<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddLocationOnPlanTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasColumn('plan', 'location')) {
            Schema::table('plan', function (Blueprint $table) {
                $table->integer('location')->nullable();
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
        if (Schema::hasColumn('plan', 'location')) {
            Schema::table('plan', function (Blueprint $table) {
                $table->dropColumn('location');
            });
        }
    }
}
