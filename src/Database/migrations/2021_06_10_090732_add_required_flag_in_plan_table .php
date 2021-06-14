<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddRequiredFlagInPlanTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('plan', function (Blueprint $table) {
            if (!Schema::hasColumn('plan', 'reuired')) {
                $table->boolean('required')->default(false);
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
        if (Schema::hasColumn('plan', 'required')) {
            Schema::table('plan', function (Blueprint $table) {
                $table->dropColumn('required');
            });
        }
    }
}
