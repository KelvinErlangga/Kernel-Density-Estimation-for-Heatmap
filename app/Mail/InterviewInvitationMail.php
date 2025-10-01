<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class InterviewInvitationMail extends Mailable
{
    use Queueable, SerializesModels;

    public $note;

    public function __construct($note)
    {
        $this->note = $note;
    }

    public function build()
    {
        return $this->subject('Undangan Wawancara')
                    ->html($this->note); // langsung kirim isi teks yang ditulis user
    }
}
