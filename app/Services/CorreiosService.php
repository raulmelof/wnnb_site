<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class CorreiosService
{
    /**
     * Calcula preços e prazos para SEDEX e PAC
     */
    public function calcular($cepOrigem, $cepDestino, $peso, $altura, $largura, $comprimento)
    {
        // Serviços: 04014 = SEDEX, 04510 = PAC
        $servicos = [
            '04014' => 'SEDEX',
            '04510' => 'PAC'
        ];

        $resultados = [];

        // A API dos Correios aceita múltiplos serviços na mesma chamada, 
        // mas para evitar erros de XML complexo, vamos chamar um por um.
        foreach ($servicos as $codigo => $nome) {
            try {
                // Parâmetros oficiais da API dos Correios
                $params = [
                    'nCdEmpresa' => '',
                    'sDsSenha' => '',
                    'nCdServico' => $codigo,
                    'sCepOrigem' => $cepOrigem,
                    'sCepDestino' => $cepDestino,
                    'nVlPeso' => max($peso, 0.3), // Mínimo 300g
                    'nCdFormato' => 1, // 1 = Caixa/Pacote
                    'nVlComprimento' => max($comprimento, 16), // Mínimo 16cm
                    'nVlAltura' => max($altura, 2), // Mínimo 2cm
                    'nVlLargura' => max($largura, 11), // Mínimo 11cm
                    'nVlDiametro' => 0,
                    'sCdMaoPropria' => 'N',
                    'nVlValorDeclarado' => 0,
                    'sCdAvisoRecebimento' => 'N',
                    'StrRetorno' => 'xml',
                    'nIndicaCalculo' => 3
                ];

                // Faz a requisição HTTP para os Correios
                $response = Http::get('http://ws.correios.com.br/calculador/CalcPrecoPrazo.asmx/CalcPrecoPrazo', $params);

                if ($response->successful()) {
                    // O Laravel não tem parse nativo de XML simples, usamos o do PHP
                    $xml = simplexml_load_string($response->body());
                    $servicoData = $xml->cServico;

                    if ((string)$servicoData->Erro == '0' || (string)$servicoData->Erro == '010') {
                        $resultados[] = [
                            'nome' => $nome,
                            'codigo' => $codigo,
                            // Converte "20,50" para 20.50
                            'valor' => (float) str_replace(',', '.', (string)$servicoData->Valor),
                            'prazo' => (string)$servicoData->PrazoEntrega . ' dias úteis',
                        ];
                    }
                }
            } catch (\Exception $e) {
                // Se falhar, apenas ignora este serviço
                continue;
            }
        }

        return $resultados;
    }
}