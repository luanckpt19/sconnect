<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class MailToMarketing extends Mailable
{
    use Queueable, SerializesModels;

    public $mail_from;
    public $mail_to;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($mail_from, $mail_to) {
        $this->mail_from = $mail_from;
        $this->mail_to = $mail_to;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build() {
        return $this->from('nthienphuong@gmail.com', 'Example')->view('mail.mail-to-mkt');
    }
}
