<?php

namespace App\Mail;

use App\Models\Pedido;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class PedidoRealizado extends Mailable
{
    use Queueable, SerializesModels;

    public $pedido; // Variável para a view acessar

    public function __construct(Pedido $pedido)
    {
        $this->pedido = $pedido;
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Recebemos seu Pedido #' . $this->pedido->id,
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.pedido-realizado', // Aponta para a view que vamos criar
        );
    }
}