<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddAllowCancelationInPlanTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('plan', function (Blueprint $table) {
            if (!Schema::hasColumn('plan', 'allow_cancelation')) {

                $table->boolean('allow_cancelation')->nullable()->default(true);
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
        Schema::table('plan', function (Blueprint $table) {
            if (Schema::hasColumn('plan', 'allow_cancelation')) {

                $table->dropColumn('allow_cancelation');
            }
        });
    }
}
