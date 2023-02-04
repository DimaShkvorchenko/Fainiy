<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddCodeAndOtherDataFieldsToUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('code')->nullable();
            $table->json('other_data')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        if (Schema::hasTable('users')) {
            if (Schema::hasColumn('users', 'code')) {
                Schema::table('users', function (Blueprint $table) {
                    $table->dropColumn('code');
                });
            }
            if (Schema::hasColumn('users', 'other_data')) {
                Schema::table('users', function (Blueprint $table) {
                    $table->dropColumn('other_data');
                });
            }
        }
    }
}
