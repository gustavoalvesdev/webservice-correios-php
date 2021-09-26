<?php 

require __DIR__ . '/vendor/autoload.php';

use \App\WebService\Correios;

// nova instância dos correios sem contrato
$obCorreios = new Correios();

$cepOrigem = '09010100';
$cepDestino = '31845010';

// executa o cálculo de frete
$frete = $obCorreios->calcularFrete(
    Correios::SERVICO_SEDEX, 
    $cepOrigem, 
    $cepDestino, 
    5, 
    Correios::FORMATO_CAIXA_PACOTE, 
    15, 
    15, 
    15
);

// verifica o resultado
if (! $frete) {
    die('Problemas ao calcular o frete');
}

// Verifica o erro
if (strlen($frete->MsgErro)) {
    die('Erro: ' . $frete->MsgErro);
}

// imprime os dados da consulta
echo 'CEP Origem: ' . $cepOrigem . "\n";
echo 'CEP Destino: ' . $cepDestino . "\n";
echo 'Valor: ' . $frete->Valor . "\n";
echo 'Prazo: ' . $frete->PrazoEntrega . "\n";
