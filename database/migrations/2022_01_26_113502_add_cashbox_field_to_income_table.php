<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddCashboxFieldToIncomeTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('income', function (Blueprint $table) {
            $table->foreignUuid('cashbox_id')->nullable()->constrained('cashboxes');
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
            if (Schema::hasColumn('income', 'cashbox_id')) {
                Schema::table('income', function (Blueprint $table) {
                    $table->dropForeign(['cashbox_id']);
                    $table->dropColumn('cashbox_id');
                });
            }
        }
    }
}
