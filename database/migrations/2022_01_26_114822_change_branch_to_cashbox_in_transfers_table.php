<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ChangeBranchToCashboxInTransfersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (Schema::hasColumn('transfers', 'from_branch_id')) {
            Schema::table('transfers', function (Blueprint $table) {
                $table->dropForeign(['from_branch_id']);
                $table->dropColumn('from_branch_id');
            });
        }
        if (Schema::hasColumn('transfers', 'to_branch_id')) {
            Schema::table('transfers', function (Blueprint $table) {
                $table->dropForeign(['to_branch_id']);
                $table->dropColumn('to_branch_id');
            });
        }
        Schema::table('transfers', function (Blueprint $table) {
            $table->foreignUuid('from_cashbox_id')->nullable()->constrained('cashboxes');
            $table->foreignUuid('to_cashbox_id')->nullable()->constrained('cashboxes');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        if (Schema::hasTable('transfers')) {
            if (Schema::hasColumn('transfers', 'from_cashbox_id')) {
                Schema::table('transfers', function (Blueprint $table) {
                    $table->dropForeign(['from_cashbox_id']);
                    $table->dropColumn('from_cashbox_id');
                });
            }
            if (Schema::hasColumn('transfers', 'to_cashbox_id')) {
                Schema::table('transfers', function (Blueprint $table) {
                    $table->dropForeign(['to_cashbox_id']);
                    $table->dropColumn('to_cashbox_id');
                });
            }
            Schema::table('transfers', function (Blueprint $table) {
                $table->foreignUuid('from_branch_id')->nullable()->constrained('branches');
                $table->foreignUuid('to_branch_id')->nullable()->constrained('branches');
            });
        }
    }
}
