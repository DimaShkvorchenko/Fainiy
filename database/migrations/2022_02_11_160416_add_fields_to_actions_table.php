<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddFieldsToActionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::dropIfExists('actions');

        Schema::create('actions', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->timestamps();
            $table->softDeletes();
            $table->enum('type', ['income', 'exchange', 'transfer', 'wallet'])->nullable();
            $table->enum('event', ['store', 'update', 'destroy'])->nullable();
            $table->double('amount', 12, 2);
            $table->uuid('parent_id');
            $table->uuid('staff_id');
            $table->uuid('client_id')->nullable();
            $table->uuid('currency_id')->nullable();
            $table->uuid('cashbox_id')->nullable();
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
        //
    }
}
