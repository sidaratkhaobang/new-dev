<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class AccidentRepairOrderMail extends Mailable
{
    use Queueable, SerializesModels;

    public $mail_data;
    public $files;
    public $mail_user;
    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($mail_data, $files, $mail_user)
    {
        $this->mail_data = $mail_data;
        $this->files = $files;
        $this->mail_user = $mail_user;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {

        $message = $this->subject($this->mail_data['topic'] )->markdown('emails.accident-repair-order-mail')
        ->with([
            'mail_data' => $this->mail_data,
            'mail_user' => $this->mail_user,
        ]);

            foreach ($this->files as $file_attachment) {
                $file_contents = $file_attachment['data'] ?? null;
                $file_name = $file_attachment['name'] ?? null;
                $file_mime = $file_attachment['mime'] ?? null;
    
                $message->attachData($file_contents, $file_name,['mime' => $file_mime]);
            }
    
            return $message;
    }
}
