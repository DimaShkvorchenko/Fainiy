<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\{Schema, DB};

class RenamePointIdToBranchIdInRatesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::dropIfExists('rates');

        $dbDriver = DB::connection()->getPDO()->getAttribute(\PDO::ATTR_DRIVER_NAME);

        Schema::create('rates', function (Blueprint $table) use ($dbDriver) {
            switch ($dbDriver) {
                case 'mysql':
                    $table->uuid('id')->primary()->default(DB::raw('(UUID())'));
                    break;
                case 'pgsql':
                    $table->uuid('id')->primary()->default(DB::raw('uuid_generate_v4()'));
                    break;
                default:
                    $table->uuid('id')->primary();
            }
            $table->timestamps();
            $table->softDeletes();
            $table->foreignUuid('from_currency_id')->constrained('currencies');
            $table->foreignUuid('to_currency_id')->constrained('currencies');
            $table->double('amount', 12, 2);
            $table->foreignUuid('branch_id')->constrained('branches');

            $table->unique(['from_currency_id','to_currency_id','branch_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('rates');
    }
}
