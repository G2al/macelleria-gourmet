<?php

namespace App\Mail;

use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class OrderConfirmedMail extends Mailable
{
    use Queueable, SerializesModels;

    public $order;

    /**
     * Crea una nuova istanza del Mailable.
     */
    public function __construct(Order $order)
    {
        $this->order = $order;
    }

    /**
     * Costruisce il messaggio email.
     */
    public function build()
    {
        return $this->subject('Conferma ordine - Polleria Gourmet')
                    ->from('g2aluigi@gmail.com', 'Polleria Gourmet')
                    ->markdown('emails.orders.confirmed');
    }
}
