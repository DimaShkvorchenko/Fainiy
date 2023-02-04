<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Models\Wallet;

class WalletInOutcome implements ShouldQueue, ShouldBeUnique
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @param  Wallet  $wallet
     * @param  float  $newAmount
     * @param  ?float  $oldAmount
     * @param  string  $operator
     * @return void
     */
    public function __construct(
        protected Wallet $wallet,
        protected float $newAmount,
        protected ?float $oldAmount,
        protected string $operator = 'plus'
    )
    {
        //
    }

    /**
     * The unique ID of the job.
     *
     * @return string
     */
    public function uniqueId()
    {
        return $this->wallet->id;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        if ($this->operator == "minus_plus") {
            $this->wallet->amount -= $this->oldAmount;
            $this->wallet->amount += $this->newAmount;
        } elseif ($this->operator == "minus") {
            $this->wallet->amount -= $this->newAmount;
        } else {
            $this->wallet->amount += $this->newAmount;
        }
        $this->wallet->save();
    }
}
