<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddSpreadFieldToExchangesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('exchanges', function (Blueprint $table) {
            $table->double('spread', 12, 2)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        if (Schema::hasTable('exchanges')) {
            if (Schema::hasColumn('exchanges', 'spread')) {
                Schema::table('exchanges', function (Blueprint $table) {
                    $table->dropColumn('spread');
                });
            }
        }
    }
}
