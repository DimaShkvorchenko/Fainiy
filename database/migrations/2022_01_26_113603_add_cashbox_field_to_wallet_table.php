<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddCashboxFieldToWalletTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('wallet', function (Blueprint $table) {
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
        if (Schema::hasTable('wallet')) {
            if (Schema::hasColumn('wallet', 'cashbox_id')) {
                Schema::table('wallet', function (Blueprint $table) {
                    $table->dropForeign(['cashbox_id']);
                    $table->dropColumn('cashbox_id');
                });
            }
        }
    }
}
