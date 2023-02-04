<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddFieldsToUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('telegram')->nullable();
            $table->json('modules')->nullable();
            $table->json('registration_data')->nullable();
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
            Schema::table('users', function (Blueprint $table) {
                if (Schema::hasColumn('users', 'telegram')) {
                    $table->dropColumn('telegram');
                }
                if (Schema::hasColumn('users', 'modules')) {
                    $table->dropColumn('modules');
                }
                if (Schema::hasColumn('users', 'registration_data')) {
                    $table->dropColumn('registration_data');
                }
            });
        }
    }
}
