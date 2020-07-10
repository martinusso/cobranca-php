<?php

namespace Cobranca\Remessa;

use Cobranca\Utils\Functions;

/**
 * Classe para geração de Arquivos de Remessa
 */
abstract class AbstractRemessa
{
    protected $conta;
    protected $params;
    protected $pagamentos = array();
    protected $remessa;
    protected $agencia;
    protected $agencia_dv;
    protected $conta_numero;
    protected $conta_dv;
    protected $nome_cedente;
    protected $convenio;
    protected $carteira;
    protected $variacao;
    protected $sequencial_remessa;
    protected $sequencial_registro = 1;

    public function __construct($conta, array $params = [])
    {
        $this->conta = $conta;
        $this->params = $params;

        $agencia = explode('-', $conta->agencia());
        $this->agencia = isset($agencia[0]) ? $agencia[0] : $conta->agencia();
        $this->agencia_dv = $this->digitoAgencia();

        $conta_numero = explode('-', $conta->contaCorrente());
        $this->conta_numero = isset($conta_numero[0]) ? $conta_numero[0] : $conta->contaCorrente();
        $this->conta_dv = isset($conta_numero[1]) ? $conta_numero[1] : '';

        $this->nome_cedente = $conta->beneficiario()->nome();
        $this->carteira = $conta->carteira();
        $this->convenio = $conta->convenio();
        $this->variacao =  $conta->variacao();
        $this->sequencial_remessa = $conta->sequencialRemessa();
    }

    public function pagamentos()
    {
        return $this->pagamentos;
    }

    public function addPagamento($pagamento)
    {
        $this->pagamentos[] = $pagamento;
    }

    public function nomeArquivo()
    {
        return 'REMESSA_' . $this->sequencial_remessa . '.rem';
    }

    protected function valorFormatado($valor, $quantidade = 11, $decimais = 2)
    {
        $valor = round($valor, 2) * 100;
        return Functions::zeros($valor, $quantidade + $decimais);
    }

    protected function calcularJurosMoraPorDiaAtraso($pagamento)
    {
        $percJurosMoraDia = ($pagamento->percentualMoraAoMes() / 100) / 30;
        $jurosMoraDia = $percJurosMoraDia * $pagamento->valor();
        return $this->valorFormatado($jurosMoraDia);
    }

    protected function digitoAgencia()
    {
        return $this->agencia_dv;
    }

    protected function digitoContaCorrente()
    {
        return $this->conta_dv;
    }


    /**
    * Retorna as linhas do arquivo de remessa
    *
    * @return string
    */
    abstract public function strings();
    abstract public function erro();
    abstract protected function getHeader();
    abstract protected function getPagamentos();
}
