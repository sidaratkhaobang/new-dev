<?php

namespace App\Jobs;

use App\Mail\SendContractExpireNotifyMail;
use App\Models\Car;
use App\Models\Customer;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Mail;

class SendContractExpireNotifyJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $contracts;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($contracts)
    {
        $this->contracts = $contracts;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
//        __log('======= START SEND MAIL =======');
        foreach ($this->contracts as $contract) {
            if (isset($contract->customer->email)) {
                $pdf = $this->getExpiredContractPdf($contract);
                Mail::to($contract->customer->email)->send(new SendContractExpireNotifyMail($pdf));
//                __log('>>> SEND MAIL TO ' . $contract->customer->email);
            }
        }
//        __log('======= END SEND MAIL =======');
    }

    public function getExpiredContractPdf($contract)
    {
        $today = Carbon::now();
        $month = $today->format('m');
        $year = $today->format('Y') + 543;

        $customer = Customer::find($contract->customer_id);
        if (!$customer) {
            return false;
        }
        $cars = Car::leftjoin('contract_lines', 'contract_lines.car_id', '=', 'cars.id')
            ->where('contract_lines.contract_id', $contract->id)
            ->select('cars.*')
            ->get();
        $pdf = Pdf::loadView(
            'admin.contracts.pdfs.contract-expired',
            [
                'contract' => $contract,
                'today' => $today,
                'customer' => $customer,
                'month' => $month,
                'year' => $year,
                'cars' => $cars
            ]
        );
        return $pdf;
    }
}
