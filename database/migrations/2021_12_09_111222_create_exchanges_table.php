<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateExchangesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('exchanges', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->timestamps();
            $table->softDeletes();
            $table->integer('access_code')->nullable();
            $table->double('from_amount', 12, 2);
            $table->double('to_amount', 12, 2);
            $table->json('commission')->nullable();
            $table->foreignUuid('user_id')->constrained();
            $table->foreignUuid('client_id')->constrained('users');
            $table->foreignUuid('point_id')->constrained();
            $table->foreignUuid('from_currency_id')->constrained('currencies');
            $table->foreignUuid('to_currency_id')->constrained('currencies');
            $table->double('exchange_rate', 12, 2);
            $table->text('description')->nullable();
            $table->string('status')->nullable();
            $table->dateTime('time_of_issue')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('exchanges');
    }
}
