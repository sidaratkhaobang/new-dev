<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class AccidentMail extends Mailable
{
    use Queueable, SerializesModels;

    public $mail_data;
    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($mail_data)
    {
        $this->mail_data = $mail_data;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->subject('แจ้งอุบัติเหตุรถ' . $this->mail_data['car_license_plate'] . ' เคส' . $this->mail_data['case'])->markdown('emails.accident-inform')
            ->with('mail_data', $this->mail_data);
    }
}
