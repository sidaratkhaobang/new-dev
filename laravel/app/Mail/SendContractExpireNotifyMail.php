<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\App;

class SendContractExpireNotifyMail extends Mailable
{
    use Queueable, SerializesModels;

    public $pdf;
    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($pdf)
    {
        $this->pdf = $pdf;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $image = 'https://uat-smartcar.obs.ap-southeast-2.myhuaweicloud.com/logo-mail.png';
        if (App::environment('production')) {
            $image = 'https://production-smartcar.obs.ap-southeast-2.myhuaweicloud.com/logo-mail.png';
        }

        $mail_data = [
            'image' => $image,
        ];

        $pdf = $this->pdf;

        return $this->markdown('emails.sendContractExpireNotifyMail')
            ->subject('แจ้งเตือนรถหมดสัญญา')
            ->with('mail_data', $mail_data)
            ->attachData($pdf->output(), 'แจ้งเตือนรถหมดสัญญา.pdf');
    }
}
