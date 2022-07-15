<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class SendMail extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct( $params )
    {
        //
        $this->meetingId = $params['meetingId'];
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this
                ->from('noreply@pobpa.com')
                ->subject('[Pobpa] Your recording is ready to download now.')
                ->view('mail')
                ->with([ 'meetingId' => $this->meetingId ]);
    }
}
