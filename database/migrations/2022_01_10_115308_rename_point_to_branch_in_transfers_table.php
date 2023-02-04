<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class RenamePointToBranchInTransfersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::dropIfExists('transfers');

        Schema::create('transfers', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->timestamps();
            $table->softDeletes();
            $table->integer('access_code')->nullable();
            $table->double('amount', 12, 2);
            $table->json('commission')->nullable();
            $table->foreignUuid('user_id')->constrained();
            $table->foreignUuid('client_id')->constrained('users');
            $table->foreignUuid('from_branch_id')->constrained('branches');
            $table->foreignUuid('to_branch_id')->constrained('branches');
            $table->foreignUuid('currency_id')->constrained();
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
        Schema::dropIfExists('transfers');
    }
}
