<?php

namespace Cobranca\Retorno\CNAB400;

use Cobranca\Banco;
use Cobranca\Utils\Functions;
use Cobranca\Retorno\AbstractRetorno;

/**
 * Layout de Arquivo Retorno CNAB400/CBR643
 */
class Santander extends AbstractRetorno
{
    public function banco()
    {
        return Banco::Santander;
    }

    public function processarRetorno($conteudoArquivo)
    {
        $i = 0;
        $conteudoArquivo = trim($conteudoArquivo);
        $registros = explode("\n", trim($conteudoArquivo));
        foreach ($registros as $linha) {
            switch (substr($linha, 0, 1)) {
                case '0': // Registro Header do Arquivo de Retorno
                    $this->lerHeader($linha);
                    break;
                case '1': // Registro Detalhe do Arquivo de Retorno
                    $i += 1;
                    $this->lerRegistroDetalhe($linha, $i);
                    break;
            }
        }
    }

    private function lerHeader($linha)
    {
        if (substr($linha, 1, 8) != '2RETORNO') {
            throw new \Exception("Tipo de Operação inválido.", 1);
        }
        if (substr($linha, 9, 10) != '01COBRANCA') {
            throw new \Exception("Tipo de Serviço inválido.", 1);
        }
        $this->setAgencia(substr($linha, 26, 4));
        $this->setContaCorrente(substr($linha, 38, 8));
        $this->setNomeCedente(trim(substr($linha, 46, 30)));
        $this->setDataMovimento(trim(substr($linha, 94, 6)));
        $this->setConvenio(substr($linha, 110, 7));
    }

    private function lerRegistroDetalhe($linha, $i)
    {
        if (substr($linha, 0, 1) != '1') {
            throw new \Exception("Registro Movimento inválido.", 1);
        }

        $tipo_cobranca = substr($linha, 107, 1);

        $data_ocorrencia = substr($linha, 110, 6);
        $data_ocorrencia = parent::parseData($data_ocorrencia);

        $natureza_recebimento = substr($linha, 108, 2);
        $ocorrencia = $this->ocorrencia($natureza_recebimento);
        if ($ocorrencia[0] == parent::LIQUIDADO) {
            $data_liquidacao = $data_ocorrencia;
        } else {
            $data_liquidacao = null;
        }

        $numero = substr($linha, 116, 10);
        $numero = intval($numero);
        $nosso_numero = substr($linha, 126, 8);
        $nossoNumeroSemDV = Functions::zeros(substr($nosso_numero, 0, -1), 12);
        $dvNossoNumero = substr($nosso_numero, -1);
        $nosso_numero = $nossoNumeroSemDV . '-' . $dvNossoNumero;

        $data_vencimento = substr($linha, 146, 6);
        $data_vencimento = parent::parseData($data_vencimento);


        $valor_titulo = substr($linha, 152, 11+2);
        $valor_titulo = (floatval($valor_titulo) / 100);

        $especie_titulo = substr($linha, 173, 2);

        $valor_tarifa = substr($linha, 175, 11+2);
        $valor_tarifa = (floatval($valor_tarifa) / 100);

        $outras_despesas = substr($linha, 188, 11+2);
        $outras_despesas = (floatval($outras_despesas) / 100);

        $juros_atraso = substr($linha, 201, 11+2);
        $juros_atraso = (floatval($juros_atraso) / 100);

        $taxa_iof = substr($linha, 214, 11+2);
        $taxa_iof = (floatval($taxa_iof) / 100);

        $valor_abatimento = substr($linha, 227, 11+2);
        $valor_abatimento = (floatval($valor_abatimento) / 100);

        $desconto_concedido = substr($linha, 240, 11+2);
        $desconto_concedido = (floatval($desconto_concedido) / 100);

        $valor_recebido = substr($linha, 253, 11+2);
        $valor_recebido = (floatval($valor_recebido) / 100);

        $juros_mora = substr($linha, 266, 11+2);
        $juros_mora = (floatval($juros_mora) / 100);

        $outros_recebimentos = substr($linha, 279, 11+2);
        $outros_recebimentos = (floatval($outros_recebimentos) / 100);

        $data_credito = substr($linha, 295, 6);
        $data_credito = parent::parseData($data_credito);

        $carteira = '';
        $variacao_carteira = '';
        $indicativo_lancamento = substr($linha, 379, 1);
        switch ($indicativo_lancamento) {
            case '1':
                $indicativo_lancamento = 'débito';
                break;
            case '2':
                $indicativo_lancamento = 'crédito';
                break;
            default:
                $indicativo_lancamento = 'sem lançamento';
                break;
        }

        $sequencial_registro = substr($linha, 394, 6); // 58 395 a 400 9(006) Seqüencial do registro

        $this->registros_detalhe[] = array(
            'id' => $i,
            'agencia' => $this->agencia(),
            'agencia_dv' => $this->agenciaDV(),
            'conta' => $this->contaCorrente(),
            'conta_dv' => $this->contaCorrenteDV(),
            'convenio' => $this->convenio(),
            'nosso_numero' => $nosso_numero,
            'numero' => $numero,
            'tipo_cobranca' => $tipo_cobranca,
            'natureza_recebimento' => $natureza_recebimento,
            'variacao_carteira' => null,
            'taxa_desconto' => null,
            'taxa_iof' => $taxa_iof,
            'carteira' => null,
            'data_movimento' => parent::dataMovimento(),
            'data_liquidacao' => $data_liquidacao,
            'data_vencimento' => $data_vencimento,
            'data_credito' => $data_credito,
            'valor_titulo' => $valor_titulo,
            'banco_recebedor' => null,
            'agencia_recebedor' => null,
            'agencia_dv_recebedor' => null,
            'especie_titulo' => $especie_titulo,
            'valor_pagto' => $valor_recebido,
            'valor_tarifa' => $valor_tarifa,
            'outras_despesas' => $outras_despesas,
            'juros_desconto' => null,
            'iof_desconto' => null,
            'valor_abatimento' => $valor_abatimento,
            'desconto' => $desconto_concedido,
            'juros_mora' => $juros_mora,
            'juros_atraso' => $juros_atraso,
            'outros_recebimentos' => $outros_recebimentos,
            'abatimento_nao_aproveitado' => null,
            'valor_lancamento' => 0,
            'indicativo_lancamento' => $indicativo_lancamento,
            'indicativo_valor' => null,
            'valor_ajuste' => null,
            'sequencial_registro' => $sequencial_registro,
            'tipo_ocorrencia' => $ocorrencia[0],
            'ocorrencia' => $ocorrencia[1]
        );
    }

    private function ocorrencia($natureza_recebimento)
    {
        $liquidado = [
            '06' => 'liquidação',
            '07' => 'liquidação por conta',
            '08' => 'liquidação por saldo',
            '17' => 'liquidado em cartório'
        ];

        $entrada = [
            '02' => 'entrada tít. confirmada'
        ];

        $baixa = [
            '09' => 'baixa automática',
            '10' => 'tít. baix. conf. instrução'
        ];

        $recusa = [
            '03' => 'entrada tít. rejeitada'
        ];

        switch ($natureza_recebimento) {
            case '02':
                $ocorrencia = isset($entrada[$natureza_recebimento]) ? $entrada[$natureza_recebimento] : '';
                return [parent::ENTRADA, $ocorrencia];
            case '03':
                $ocorrencia = isset($recusa[$natureza_recebimento]) ? $recusa[$natureza_recebimento] : '';
                return [parent::RECUSA, $ocorrencia];
            case '05':
            case '06':
            case '07':
            case '08':
            case '15':
            case '46':
                $ocorrencia = isset($liquidado[$natureza_recebimento]) ? $liquidado[$natureza_recebimento] : '';
                return [parent::LIQUIDADO, $ocorrencia];
            case '09':
            case '10':
            case '20':
                $ocorrencia = isset($baixa[$natureza_recebimento]) ? $baixa[$natureza_recebimento] : '';
                return [parent::BAIXA, $ocorrencia];
            case '28':
                return [parent::MANUTENCAO_TITULO_VENCIDO, parent::DESC_MANUTENCAO_TITULO_VENCIDO];
            case '72':
                $ocorrencia = isset($alteracaoTipoCobranca[$natureza_recebimento]) ? $alteracaoTipoCobranca[$natureza_recebimento] : '';
                return [parent::ALTERACAO_TIPO, $ocorrencia];
            default:
                return ['', ''];
        }
    }

    /**
    * Ler Registro Trailler de Arquivo
    */
    private function lerRegistroTrailler($linha)
    {
        if (substr($linha, 0, 1) != '9') {
            throw new \Exception("Registro Trailler de Arquivo  inválido.", 1);
        }
    }
}
