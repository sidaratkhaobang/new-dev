<?php

namespace App\Console\Commands;

use App\Enums\ContractEnum;
use App\Jobs\SendContractExpireNotifyJob;
use App\Models\Contracts;
use App\Models\LongTermRental;
use App\Models\Rental;
use Carbon;
use Illuminate\Console\Command;
use Illuminate\Database\Eloquent\Builder;

class CheckContractExpireCommand extends Command
{

    protected $signature = 'command:check_contract_expire';
    protected $description = 'Command Check Contract Expire';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $contracts = Contracts::whereHasMorph(
            'job',
            [Rental::class, LongTermRental::class],
            function (Builder $query, string $type) {
                if (strcmp($type, Rental::class) === 0) {
                    $query->whereDate('return_date', '=', Carbon::now()->addMonth(1)->format('Y-m-d'));
                    $query->orWhereDate('return_date', '=', Carbon::now()->addMonth(4)->format('Y-m-d'));
                }
                if (strcmp($type, LongTermRental::class) === 0) {
                    $query->whereDate('contract_end_date', '=', Carbon::now()->addMonth(1)->format('Y-m-d'));
                    $query->orWhereDate('contract_end_date', '=', Carbon::now()->addMonth(4)->format('Y-m-d'));
                }
            }
        )
            ->join('customers', 'customers.id', '=', 'contracts.customer_id')
            ->where('contracts.status', ContractEnum::ACTIVE_BETWEEN_CONTRACT)
            ->whereNotNull('customers.email')
            ->select(['contracts.*', 'customers.email'])
            ->get();

        foreach ($contracts as $contract) {
            $this->comment($contract->worksheet_no);
            if (strcmp($contract->job_type, LongTermRental::class) === 0) {
                $contract->contract_start_date = $contract->job->contract_start_date;
                $contract->contract_end_date = $contract->job->contract_end_date;
            }

            if (strcmp($contract->job_type, Rental::class) === 0) {
                $contract->contract_start_date = $contract->job->pickup_date;
                $contract->contract_end_date = $contract->job->return_date;
            }
        }

        SendContractExpireNotifyJob::dispatch($contracts);
        return 0;
    }
}
