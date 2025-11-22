<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Pedido;
use App\Models\ProdutoVariacao;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class LimparPedidosExpirados extends Command
{
    /**
     * O nome e a assinatura do comando no terminal.
     * Ex: php artisan loja:limpar-pedidos
     */
    protected $signature = 'loja:limpar-pedidos';

    /**
     * A descrição do comando.
     */
    protected $description = 'Cancela pedidos pendentes há mais de 24h e restaura o estoque.';

    /**
     * Executa o comando.
     */
    public function handle()
    {
        // 1. Define o limite de tempo (ex: 24 horas atrás)
        $tempoLimite = Carbon::now()->subHours(24);

        $this->info("Buscando pedidos pendentes criados antes de: " . $tempoLimite->toDateTimeString());

        // 2. Busca os pedidos 'aguardando_pagamento' vencidos
        $pedidosExpirados = Pedido::where('status', 'aguardando_pagamento')
                                  ->where('created_at', '<', $tempoLimite)
                                  ->get();

        if ($pedidosExpirados->isEmpty()) {
            $this->info('Nenhum pedido expirado encontrado.');
            return Command::SUCCESS;
        }

        $this->info("Encontrados {$pedidosExpirados->count()} pedidos para cancelar.");

        // 3. Processa cada pedido
        foreach ($pedidosExpirados as $pedido) {
            try {
                // APENAS MUDAMOS O STATUS.
                // Não mexemos no estoque pois, na nossa lógica atual,
                // o estoque só sai quando o pagamento é confirmado.
                $pedido->update(['status' => 'cancelado']);

                $this->info("Pedido #{$pedido->id} cancelado automaticamente (Expirado).");

            } catch (\Exception $e) {
                $this->error("Erro ao cancelar pedido #{$pedido->id}: " . $e->getMessage());
            }
        }

        return Command::SUCCESS;
    }
}