<?php

namespace Cobranca;

/**
 * Conta
 */
class Conta
{
    private $banco;
    private $agencia;
    private $carteira;
    private $variacao;
    private $conta_corrente;
    private $convenio;
    private $sequencial_remessa;
    private $beneficiario;
    private $especie_titulo;

    /**
    * Construtor
    *
    * @param string $banco
    * @param string $agencia
    * @param string $conta_corrente
    * @param string $carteira
    * @param string $variacao
    * @param string $convenio
    * @param string $beneficiario
    * @param string $especie_titulo = null
    * @param string $aceite = null
    * @param string $sequencial_remessa = null
    */
    public function __construct($banco, $agencia, $conta_corrente, $carteira, $variacao, $convenio, $beneficiario, string $especie_titulo = null, $aceite = null, $sequencial_remessa = null)
    {
        $this->banco = $banco;
        $this->agencia = $agencia;
        $this->conta_corrente = $conta_corrente;
        $this->carteira = $carteira;
        $this->variacao = $variacao;
        $this->convenio = $convenio;
        $this->beneficiario = $beneficiario;
        if (!empty($especie_titulo)) {
            $this->especie_titulo = $especie_titulo;
            $this->validaEspecieTitulo();
        }
        $this->aceite = $aceite;
        $this->sequencial_remessa = $sequencial_remessa;
    }

    /**
    * Aceite do título
    *  N - Sem aceite
    *  A - Com aceite - Indica o reconhecimento formal (assinatura no documento) do sacado no título
    *
    * @return string
    */
    public function aceite()
    {
        if (!$this->aceite) {
            return 'N';
        }
        if ($this->aceite == 'S') {
            return 'A';
        }
        return $this->aceite;
    }

    /**
    * Agencia
    *
    * @return string
    */
    public function agencia()
    {
        return $this->agencia;
    }

    /**
    * Banco
    *
    * @return string
    */
    public function banco()
    {
        return $this->banco;
    }

    /**
    * Conta corrente
    *
    * @return string
    */
    public function contaCorrente()
    {
        return $this->conta_corrente;
    }

    /**
    * Carteira
    *
    * @return string
    */
    public function carteira()
    {
        return $this->carteira;
    }

    /**
    * Variação
    *
    * @return string
    */
    public function variacao()
    {
        return $this->variacao;
    }

    /**
    * Convênio
    *
    * @return string
    */
    public function convenio()
    {
        return $this->convenio;
    }

    /**
    * Beneficiário
    *
    * @return string
    */
    public function beneficiario()
    {
        return $this->beneficiario;
    }

    /**
    * Espécie de Título
    * Banco do Brasil
    * 01 - Duplicata Mercantil
    * 02 - Nota Promissória
    * 03 - Nota de Seguro
    * 05 – Recibo
    * 08 - Letra de Câmbio
    * 09 – Warrant
    * 10 – Cheque
    * 12 - Duplicata de Serviço
    * 13 - Nota de Débito
    * 15 - Apólice de Seguro
    * 25 - Dívida Ativa da União
    * 26 - Dívida Ativa de Estado
    * 27 - Dívida Ativa de Município
    *
    * Santander - 033
    * 01 = DUPLICATA
    * 02 = NOTA PROMISSÓRIA
    * 03 = APÓLICE / NOTA DE SEGURO
    * 05 = RECIBO
    * 06 = DUPLICATA DE SERVIÇO
    * 07 = LETRA DE CAMBIO
    * 08 = BDP - BOLETO DE PROPOSTA - ( NOTA 6)
    * 19 = BCC – BOLETO CARTÃO DE CRÉDITO ( NOTA 8)
    *
    * @return string
    */
    public function especieTitulo()
    {
        return $this->especie_titulo;
    }

    /**
    * Valida a Espécie de Título
    * 01 - Duplicata Mercantil
    * 02 - Nota Promissória
    * 03 - Nota de Seguro
    * 05 – Recibo
    * 08 - Letra de Câmbio
    * 09 – Warrant
    * 10 – Cheque
    * 12 - Duplicata de Serviço
    * 13 - Nota de Débito
    * 15 - Apólice de Seguro
    * 25 - Dívida Ativa da União
    * 26 - Dívida Ativa de Estado
    * 27 - Dívida Ativa de Município
    *
    * @throws InvalidArgumentException
    */
    private function validaEspecieTitulo()
    {
        $valores_validos = array('01', '02', '03', '05', '08', '09', '10', '12', '13', '15', '25', '26', '27');
        if (!in_array($this->especie_titulo, $valores_validos)) {
            throw new \InvalidArgumentException("Espécie de Título {$this->especie_titulo} inválido", 1);
        }
    }

    /**
    * Sequencial remessa
    *
    * @return string
    */
    public function sequencialRemessa()
    {
        return $this->sequencial_remessa;
    }
}
