<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class CorreiosService
{
    public function calcular($cepOrigem, $cepDestino, $peso, $altura, $largura, $comprimento)
    {
        // Serviços
        $servicos = [
            '04014' => 'SEDEX',
            '04510' => 'PAC'
        ];

        $resultados = [];
        $usouContingencia = false;

        foreach ($servicos as $codigo => $nome) {
            try {
                $params = [
                    'nCdEmpresa' => '',
                    'sDsSenha' => '',
                    'nCdServico' => $codigo,
                    'sCepOrigem' => $cepOrigem,
                    'sCepDestino' => $cepDestino,
                    'nVlPeso' => number_format(max($peso, 0.3), 2, '.', ''),
                    'nCdFormato' => 1, 
                    'nVlComprimento' => max($comprimento, 16),
                    'nVlAltura' => max($altura, 2),
                    'nVlLargura' => max($largura, 11),
                    'nVlDiametro' => 0,
                    'sCdMaoPropria' => 'N',
                    'nVlValorDeclarado' => 0,
                    'sCdAvisoRecebimento' => 'N',
                    'StrRetorno' => 'xml',
                    'nIndicaCalculo' => 3
                ];

                // Tenta conectar (Timeout curto de 5s para não travar o site)
                $response = Http::timeout(5)
                    ->connectTimeout(2)
                    ->withoutVerifying()
                    ->withOptions(['ip_resolve' => 1])
                    ->get('https://ws.correios.com.br/calculador/CalcPrecoPrazo.asmx/CalcPrecoPrazo', $params);

                if ($response->successful()) {
                    $xml = simplexml_load_string($response->body());
                    $servicoData = $xml->cServico;

                    if ((string)$servicoData->Erro == '0' || (string)$servicoData->Erro == '010') {
                        $resultados[] = [
                            'nome' => $nome,
                            'codigo' => $codigo,
                            'valor' => (float) str_replace(',', '.', (string)$servicoData->Valor),
                            'prazo' => (string)$servicoData->PrazoEntrega . ' dias úteis',
                        ];
                    }
                } else {
                    throw new \Exception("API Falhou");
                }

            } catch (\Exception $e) {
                // SE FALHAR (O que está acontecendo), ATIVA A CONTINGÊNCIA
                $usouContingencia = true;
                Log::error("Correios Falhou, usando contingência: " . $e->getMessage());
            }
        }

        // Se a API falhou para todos ou algum serviço, gera valores matemáticos
        // para você não ficar travado no desenvolvimento.
        if (empty($resultados) || $usouContingencia) {
            return $this->calcularFreteContingencia($peso);
        }

        return $resultados;
    }

    /**
     * Gera um frete simulado baseado no peso para testes
     */
    private function calcularFreteContingencia($peso)
    {
        // Lógica simples: Base + (Peso * Taxa)
        $precoSedex = 25.00 + ($peso * 5.00);
        $precoPac = 15.00 + ($peso * 3.00);

        return [
            [
                'nome' => 'SEDEX (Estimado)',
                'codigo' => '04014',
                'valor' => $precoSedex,
                'prazo' => '3 dias úteis',
            ],
            [
                'nome' => 'PAC (Estimado)',
                'codigo' => '04510',
                'valor' => $precoPac,
                'prazo' => '7 dias úteis',
            ]
        ];
    }
}