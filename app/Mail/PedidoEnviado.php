<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class PedidoEnviado extends Mailable
{
    use Queueable, SerializesModels;
    public $pedido;

    public function __construct($pedido) { $this->pedido = $pedido; }

    public function envelope(): Envelope {
        return new Envelope(subject: 'Seu pedido #' . $this->pedido->id . ' está a caminho!');
    }

    public function content(): Content {
        return new Content(view: 'emails.pedido-enviado');
    }
}