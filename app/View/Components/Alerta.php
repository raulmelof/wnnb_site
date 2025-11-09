<?php

namespace App\View\Components;

use Illuminate\View\Component;

class Alerta extends Component
{
    public string $type;
    public string $message;

    /**
     * Create a new component instance.
     *
     * @return void
     */
    public function __construct($type = 'info', $message)
    {
        $this->type = $type;
        $this->message = $message;
    }

    /**
     * Retorna a classe CSS do Bootstrap correspondente ao tipo de alerta.
     */
    public function alertClass(): string
    {
        $classes = [
            'info' => 'alert-info',
            'success' => 'alert-success',
            'warning' => 'alert-warning',
            'danger' => 'alert-danger',
        ];
        return 'alert ' . ($classes[$this->type] ?? 'alert-info');
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\Contracts\View\View|\Closure|string
     */
    public function render()
    {
        return view('components.alerta');
    }
}