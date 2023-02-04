<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddCommissionProfitFieldsToIncomeTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('income', function (Blueprint $table) {
            $table->double('commission', 12, 2)->nullable();
            $table->double('profit', 12, 2)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        if (Schema::hasTable('income')) {
            if (Schema::hasColumn('income', 'commission')) {
                Schema::table('income', function (Blueprint $table) {
                    $table->dropColumn('commission');
                });
            }
            if (Schema::hasColumn('income', 'profit')) {
                Schema::table('income', function (Blueprint $table) {
                    $table->dropColumn('profit');
                });
            }
        }
    }
}
