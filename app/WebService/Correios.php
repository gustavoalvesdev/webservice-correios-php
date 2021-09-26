<?php 

namespace App\WebService;

class Correios
{   
    /**
     * URL base da API
     * @var string
     */
    const URL_BASE = 'http://ws.correios.com.br';

    /**
     * Códigos de serviço dos Correios
     * @var string
     */
    const SERVICO_SEDEX = '04014';
    const SERVICO_SEDEX_12 = '04782';
    const SERVICO_SEDEX_10 = '04790';
    const SERVICO_SEDEX_HOJE = '04804';
    CONST SERVICO_PAC = '04510';

    /**
     * Códigos os formatos dos Correios
     * @var integer
     */
    const FORMATO_CAIXA_PACOTE = 1;
    const FORMATO_ROLO_PRISMA = 2;
    const FORMATO_ENVELOPE = 3;

    /**
     * Código da empresa com contrato
     *
     * @var string
     */
    private $codigoEmpresa = '';

    /**
     * Senha da empresa com contrato
     *
     * @var string
     */
    private $senhaEmpresa = '';
    
    /**
     * Método responsável pela definição dos dados de contrato do WebService dos Correios
     *
     * @param string $codigoEmpresa
     * @param string $senhaEmpresa
     */
    public function __construct($codigoEmpresa = '', $senhaEmpresa = '')
    {
        $this->codigoEmpresa = $codigoEmpresa;
        $this->senhaEmpresa = $senhaEmpresa;
    }

    /**
     * Método responsável por calcular  o frete nos Correios
     *
     * @param string $codigoServico
     * @param string $cepOrigem
     * @param string $cepDestino
     * @param float $peso
     * @param integer $formato
     * @param integer $comprimento
     * @param integer $altura
     * @param integer $largura
     * @param integer $diametro
     * @param boolean $maoPropria
     * @param integer $valorDeclarado
     * @param boolean $avisoRecebimeto
     * @return object
     */
    public function calcularFrete(
        $codigoServico, 
        $cepOrigem, 
        $cepDestino, 
        $peso, 
        $formato, 
        $comprimento, 
        $altura, 
        $largura,
        $diametro = 0,
        $maoPropria = false,
        $valorDeclarado = 0,
        $avisoRecebimeto = false
    )
    {
        // Parâmetros da URL de cálculo
        $parametros = [
            'nCdEmpresa'          => $this->codigoEmpresa,
            'sDsSenha'            => $this->senhaEmpresa,
            'nCdServico'          => $codigoServico,
            'sCepOrigem'         => $cepDestino,
            'sCepDestino'         => $cepOrigem,
            'nVlPeso'             => $peso,
            'nCdFormato'          => $formato,
            'nVlComprimento'      => $comprimento,
            'nVlAltura'           => $altura,
            'nVlLargura'          => $largura,
            'nVlDiametro'         => $diametro,
            'sCdMaoPropria'       => $maoPropria ? 'S' : 'N',
            'nVlValorDeclarado'   => $valorDeclarado,
            'sCdAvisoRecebimento' => $avisoRecebimeto ? 'S' : 'N',
            'StrRetorno'          => 'xml'
        ];

        // query string
        $query = http_build_query($parametros);

        // executa a consulta de frete
        $resultado = $this->get('/calculador/CalcPrecoPrazo.aspx?' . $query);

        // retorna os dados do frete calculado
        return $resultado ? $resultado->cServico : null;
    }   

    /**
     * Método responsável por executar a consulta GET no WebService dos Correios
     *
     * @param string $resource
     * @return object
     */
    public function get($resource)
    {
        // endpoint completo
        $endpoint = self::URL_BASE . $resource;
        
        // inicia o CURL
        $curl = curl_init();

        // configurações do CURL
        curl_setopt_array($curl, [
            CURLOPT_URL => $endpoint,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_CUSTOMREQUEST => 'GET'
        ]);

        // executa a consulta CURL
        $response = curl_exec($curl);

        // fecha a conexão do CURL
        curl_close($curl);

        // retorna o xml instanciado
        return strlen($response) ? simplexml_load_string($response) : null;
    }
}
