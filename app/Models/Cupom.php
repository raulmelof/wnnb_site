<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Cupom extends Model
{
    use HasFactory;

    // Corrige o nome da tabela (para não buscar 'cupoms')
    protected $table = 'cupons';

    protected $fillable = [
        'codigo',
        'tipo',
        'valor',
        'validade',
        'limite_uso',
        'usos_atuais',
        'ativo',
    ];

    /**
     * Verifica se o cupom é válido para uso agora.
     */
    public function estaValido()
    {
        // 1. Verifica se está ativo manualmente
        if (!$this->ativo) return false;

        // 2. Verifica a data de validade (se houver)
        if ($this->validade && now()->gt($this->validade)) return false;

        // 3. Verifica o limite de uso (se houver)
        if ($this->limite_uso && $this->usos_atuais >= $this->limite_uso) return false;

        return true;
    }
}