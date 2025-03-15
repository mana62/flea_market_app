<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use App\Models\ChatRoom;

class TransactionCompletedMail extends Mailable
{
    use Queueable, SerializesModels;

    public $chatRoom;

    /**
     * Create a new message instance.
     */
    public function __construct(ChatRoom $chatRoom)
    {
        $this->chatRoom = $chatRoom;
    }

    /**
     * Build the message.
     */
    public function build()
    {
        return $this->subject('【評価されました】')
                    ->view('transaction_completed');
    }
}
