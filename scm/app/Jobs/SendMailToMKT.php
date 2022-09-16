<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Mail;
use Exception;

use App\Mail\MailToMarketing;

class SendMailToMKT implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $mail_from;
    public $mail_to;
    public $subject;
    public $message;
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(array $mail_from, array $mail_to, $subject, $message) {
        $this->mail_from = $mail_from;
        $this->mail_to = $mail_to;
        $this->subject = $subject;
        $this->message = $message;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle() {

        try {
            $data = ['receiver_name'=>$this->mail_to['name'], 'body'=>$this->message];
            Mail::send('mail.mail-to-mkt', $data, function($message) {
                $message->subject($this->subject);
                $message->replyto($this->mail_from['address'], $this->mail_from['name']);
                $message->to($this->mail_to['address'], $this->mail_to['name']);            
            });
        } catch (Exception $e) {
            Log::info('SendMailToMKT error: ' . $e->getMessage());
        }
    }
}
