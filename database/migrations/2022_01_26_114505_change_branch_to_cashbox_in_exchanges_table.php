<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ChangeBranchToCashboxInExchangesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (Schema::hasColumn('exchanges', 'branch_id')) {
            Schema::table('exchanges', function (Blueprint $table) {
                $table->dropForeign(['branch_id']);
                $table->dropColumn('branch_id');
            });
        }
        Schema::table('exchanges', function (Blueprint $table) {
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
        if (Schema::hasTable('exchanges')) {
            if (Schema::hasColumn('exchanges', 'cashbox_id')) {
                Schema::table('exchanges', function (Blueprint $table) {
                    $table->dropForeign(['cashbox_id']);
                    $table->dropColumn('cashbox_id');
                });
            }
            Schema::table('exchanges', function (Blueprint $table) {
                $table->foreignUuid('branch_id')->nullable()->constrained('branches');
            });
        }
    }
}
