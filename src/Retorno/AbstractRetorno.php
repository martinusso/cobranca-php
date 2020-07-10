<?php

namespace Cobranca\Retorno;

/**
 * Classe base de Retorno
 */
abstract class AbstractRetorno
{
    const LIQUIDADO = 'LIQUIDADO';
    const ENTRADA = 'ENTRADA';
    const BAIXA = 'BAIXA';
    const RECUSA = 'RECUSA';
    const ALTERACAO_TIPO = 'ALTERACAO_TIPO';
    const MANUTENCAO_TITULO_VENCIDO = 'MANUTENCAO_TITULO_VENCIDO';
    const DESC_MANUTENCAO_TITULO_VENCIDO = 'Manutenção de título vencido';

    private $agencia;
    private $agencia_dv;
    private $conta_corrente;
    private $conta_corrente_dv;
    private $dataMovimento;
    private $nome_cedente;
    private $convenio;
    protected $registros_detalhe = array();

    abstract public function banco();
    abstract public function processarRetorno($conteudoArquivo);

    public function parseData($data)
    {
        if ($data == '000000') {
            return '';
        }
        return \DateTime::createFromFormat('dmy', $data)->format('Y-m-d');
    }

    public function agencia()
    {
        return $this->agencia;
    }

    protected function setAgencia($value)
    {
        $this->agencia = $value;
    }

    public function agenciaDV()
    {
        return $this->agencia_dv;
    }

    protected function setAgenciaDV($value)
    {
        $this->agencia_dv = $value;
    }

    public function contaCorrente()
    {
        return $this->conta_corrente;
    }

    protected function setContaCorrente($value)
    {
        $this->conta_corrente = $value;
    }

    public function contaCorrenteDV()
    {
        return $this->conta_corrente_dv;
    }

    protected function setContaCorrenteDV($value)
    {
        $this->conta_corrente_dv = $value;
    }

    public function dataMovimento()
    {
        return $this->dataMovimento;
    }

    protected function setDataMovimento($value)
    {
        $data = $this->parseData($value);
        $this->dataMovimento = $data;
    }

    public function nomeCedente()
    {
        return $this->nome_cedente;
    }

    protected function setNomeCedente($value)
    {
        $this->nome_cedente = $value;
    }

    public function convenio()
    {
        return $this->convenio;
    }

    protected function setConvenio($value)
    {
        $this->convenio = $value;
    }

    public function registrosDetalhe()
    {
        return $this->registros_detalhe;
    }
}
