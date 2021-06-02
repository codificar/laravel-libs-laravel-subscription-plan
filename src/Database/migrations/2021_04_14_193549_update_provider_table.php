<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateProviderTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('provider', function (Blueprint $table) {
            //
            if (!Schema::hasColumn('provider', 'signature_id')) {
                $table->integer('signature_id')->nullable();
            }

            if (!Schema::hasColumn('provider', 'document')) {
                $table->string("document")->nullable()->default(null);
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
		Schema::table('provider',function($table){
			$table->dropColumn("signature_id");
			$table->dropColumn("document");
		});
    }
}