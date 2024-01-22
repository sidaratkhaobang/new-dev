<?php

namespace App\Console\Commands;

use App\Enums\InsuranceCarStatusEnum;
use App\Enums\OwnershipTransferStatusEnum;
use Illuminate\Console\Command;
use App\Models\CMI;
use App\Jobs\ExpiredCmiJob;
use App\Models\ChangeRegistration;
use App\Models\HirePurchase;
use App\Models\OwnershipTransfer;
use Carbon;
use Illuminate\Support\Facades\Log;

class ChangeStatusOwnershipTransfer extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'tls:change_status_ownership_transfer';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

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
        $today = date('Y-m-d');
        $addDay = date('Y-m-d', strtotime("+30 days"));
        $hire_purchase_list = HirePurchase::leftjoin('ownership_transfers', 'ownership_transfers.hire_purchase_id', '=', 'hire_purchases.id')
            ->where('ownership_transfers.status', OwnershipTransferStatusEnum::WAITING_TRANSFER)
            ->whereDate('hire_purchases.actual_last_payment_date', '<=', $addDay)
            ->select('hire_purchases.id', 'ownership_transfers.id as ownership_transfer_id')
            ->get();
        if ($hire_purchase_list) {
            foreach ($hire_purchase_list as $key => $item) {
                $ownership_transfer = OwnershipTransfer::find($item->ownership_transfer_id);
                $ownership_transfer->status = OwnershipTransferStatusEnum::WAITING_DOCUMENT_TRANSFER;
                $ownership_transfer->save();
            }
        } else {
            Log::channel('sentry')->alert('OwnershipTransfer change status failed', []);
        }
        return true;
    }
}
