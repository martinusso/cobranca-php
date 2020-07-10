<?php

namespace Cobranca\Retorno\CNAB400;

use Cobranca\Utils\Functions;
use Cobranca\Retorno\AbstractRetorno;

/**
 * Layout de Arquivo Retorno CNAB400/CBR643
 */
class BancoBrasil extends AbstractRetorno
{
    public function banco()
    {
        return '001';
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
                case '7': // Registro Detalhe do Arquivo de Retorno
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
        // 01 001 a 001 9(001) Identificação do Registro Header: “0”
        // 02 002 a 002 9(001) Tipo de Operação: “2”
        // 003 a 009 X(007) Identificação Tipo de Operação “RETORNO”
        // 04 010 a 011 9(002) Identificação do Tipo de Serviço: “01”
        // 05 012 a 019 X(008) Identificação por Extenso do Tipo de Serviço: “COBRANCA”
        // 06 020 a 026 X(007) Complemento do Registro: “Brancos”
        $this->setAgencia(substr($linha, 26, 4)); // 07 027 a 030 9(004) Prefixo da Agência: Número da Agência onde está cadastrado o convênio líder do cedente
        $this->setAgenciaDV(substr($linha, 30, 1)); // 08 031 a 031 X(001) Dígito Verificador - D.V. - do Prefixo da Agência.
        $this->setContaCorrente(substr($linha, 31, 8)); // 09 032 a 039 9(008) Número da Conta Corrente: Número da conta onde está cadastrado o Convênio Líder do Cedente
        $this->setContaCorrenteDV(substr($linha, 39, 1)); // 10 040 a 040 X(001) Dígito Verificador - D.V. - da Conta Corrente do Cedente
        // 11 041 a 046 9(006) Zeros
        $this->setNomeCedente(trim(substr($linha, 46, 30))); // 12 047 a 076 X(030) Nome do Cedente
        // 13 077 a 094 X(018) 001BANCODOBRASIL
        $this->setDataMovimento(trim(substr($linha, 94, 6))); // 14 095 a 100 9(006) Data da Gravação: Informe no formado “DDMMAA”
        // 15 101 a 107 9(007) Seqüencial do Retorno 01
        // 16 108 a 149 X(042) Complemento do Registro: “Brancos”
        $this->setConvenio(substr($linha, 149, 7)); // 17 150 a 156 9(007) Número de convênio
        // 18 157 a 394 X(238) Complemento do Registro: “Brancos”
        // 19 395 a 400 9(006) Nr. Seqüencial do registro
    }

    private function lerRegistroDetalhe($linha, $i)
    {
        if (substr($linha, 0, 1) != '7') {
            throw new \Exception("Registro Detalhe inválido.", 1);
        }

        // 01 001 a 001 9(001) Identificação do Registro Detalhe: 7
        // 02 002 a 003 9(002) Zeros
        // 03 004 a 017 9(014) Zeros
        $agencia = substr($linha, 17, 4); // 04 018 a 021 9(004) Prefixo da Agência
        $agencia_dv = substr($linha, 21, 1); // 05 022 a 022 X(001) Dígito Verificador - D.V. - do Prefixo da Agência
        $conta = substr($linha, 22, 8); // 06 023 a 030 9(008) Número da Conta Corrente do Cedente
        $conta_dv = substr($linha, 30, 1); // 07 031 a 031 X(001) Dígito Verificador - D.V. - do Número da Conta Corrente do Cedente
        $convenio = substr($linha, 31, 7);// 08 032 a 038 9(007) Número do Convênio de Cobrança do Cedente
        // 09 039 a 063 X(025) Número de Controle do Participante
        $nosso_numero = substr($linha, 63, 17); // 10 064 a 080 9(017) Nosso-Número
        $tipo_cobranca = substr($linha, 80, 1); // 11 081 a 081 9(001) Tipo de cobrança
        // 12 082 a 082 9(001) Tipo de cobrança específico para comando 72 (alteração de tipo de cobrança de títulos das carteiras  11 e 17)
        // 13 083 a 086 9(004) Dias para cálculo
        $natureza_recebimento = substr($linha, 86, 2); // 14 087 a 088 9(002) Natureza do recebimento
        // 15 089 a 091 X(003) Prefixo do Título
        $variacao_carteira = substr($linha, 91, 3); // 16 092 a 094 9(003) Variação da Carteira
        // 17 095 a 095 9(001) Conta Caução
        $taxa_desconto = substr($linha, 95, 5); // 18 096 a 100 9(005) Taxa para desconto
        $taxa_desconto = (floatval($taxa_desconto) / 100);
        $taxa_iof = substr($linha, 100, 4); // 19 101 a 105 9(004) Taxa IOF
        $taxa_iof = (floatval($taxa_iof) / 100);
        // 20 106 a 106 9(001) Branco
        $carteira = substr($linha, 106, 2); // 21 107 a 108 9(002) Carteira
        $comando = substr($linha, 108, 2); // 22 109 a 110 9(002) Comando

        $ocorrencia = $this->ocorrencia($comando, $natureza_recebimento);
        if ($ocorrencia[0] == parent::LIQUIDADO) {
            $data_liquidacao = substr($linha, 110, 6); // 23 111 a 116 9(006) Data de liquidação (DDMMAA)
            $data_liquidacao = parent::parseData($data_liquidacao);
        } else {
            $data_liquidacao = null;
        }
        $numero = substr($linha, 116, 10); // 24 117 a 126 X(010) Número do título dado pelo cedente
        // 25 127 a 146 X(020) Brancos
        $data_vencimento = substr($linha, 146, 6); // 26 147 a 152 9(006) Data de vencimento (DDMMAA)
        $data_vencimento = parent::parseData($data_vencimento);
        $valor_titulo = substr($linha, 152, 11+2); // 27 153 a 165 9(011) v99 Valor do título
        $valor_titulo = (floatval($valor_titulo) / 100);
        $banco_recebedor = substr($linha, 165, 3); // 28 166 a 168 9(003) Código do banco recebedor
        $agencia_recebedor = substr($linha, 168, 4); // 29 169 a 172 9(004) Prefixo da agência recebedora
        $agencia_dv_recebedor = substr($linha, 172, 1); // 30 173 a 173 X(001) DV prefixo recebedora
        $especie_titulo = substr($linha, 173, 2); // 31 174 a 175 9(002) Espécie do título
        $data_credito = substr($linha, 175, 6); // 32 176 a 181  9(006) Data do crédito (DDMMAA)
        $data_credito = parent::parseData($data_credito);
        $valor_tarifa = substr($linha, 181, 5+2); // 33 182 a 188 9(005) v99 Valor da tarifa
        $valor_tarifa = (floatval($valor_tarifa) / 100);
        $outras_despesas = substr($linha, 188, 11+2); // 34 189 a 201 9(011) v99 Outras despesas
        $outras_despesas = (floatval($outras_despesas) / 100);
        $juros_desconto = substr($linha, 201, 11+2); // 35 202 a 214 9(011) v99 Juros do desconto
        $juros_desconto = (floatval($juros_desconto) / 100);
        $iof_desconto = substr($linha, 214, 11+2); // 36 215 a 227  9(011) v99 IOF do desconto
        $iof_desconto = (floatval($iof_desconto) / 100);
        $valor_abatimento = substr($linha, 227, 11+2); // 37 228 a 240 9(011) v99 Valor do abatimento
        $valor_abatimento = (floatval($valor_abatimento) / 100);
        $desconto_concedido = substr($linha, 240, 11+2); // 38 241 a 253 9(011) v99 Desconto concedido  (diferença entre valor do título e valor recebido)
        $desconto_concedido = (floatval($desconto_concedido) / 100);
        $valor_recebido = substr($linha, 253, 11+2); // 39 254 a 266 9(011) v99 Valor recebido (valor recebido parcial)
        $valor_recebido = (floatval($valor_recebido) / 100);
        $juros_mora = substr($linha, 266, 11+2); // 40 267 a 279 9(011) v99 Juros de mora
        $juros_mora = (floatval($juros_mora) / 100);
        $outros_recebimentos = substr($linha, 279, 11+2); // 41 280 a 292 9(011) v99 Outros recebimentos
        $outros_recebimentos = (floatval($outros_recebimentos) / 100);
        $abatimento_nao_aproveitado = substr($linha, 292, 11+2); // 42 293 a 305 9(011) v99 Abatimento não aproveitado pelo sacado
        $abatimento_nao_aproveitado = (floatval($abatimento_nao_aproveitado) / 100);
        $valor_lancamento = substr($linha, 305, 11+2); // 43 306 a 318 9(011) v99 Valor do lançamento
        $valor_lancamento = (floatval($valor_lancamento) / 100);
        $indicativo_lancamento = substr($linha, 318, 1); // 44 319 a 319 9(001) Indicativo de débito/crédito
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

        $indicativo_valor = substr($linha, 319, 1); // 45 320 a 320 9(001) Indicador de valor
        $valor_ajuste = substr($linha, 320, 10); // 46 321 a 332 9(010) v99 Valor do ajuste
        $valor_ajuste = (floatval($valor_ajuste) / 100);
        // 47 333 a 333 X(001) Brancos (vide observação para cobrança compartilhada)
        // 48 334 a 342 9(009) Brancos (vide observação para cobrança compartilhada)
        // 49 343 a 349 9(007) Zeros  (vide observação para cobrança compartilhada)
        // 50 350 a 358  9(009) Zeros  (vide observação para cobrança compartilhada)
        // 51 359 a 365 9(007) Zeros  (vide observação para cobrança compartilhada)
        // 52 366 a 374 9(009) Zeros  (vide observação para cobrança compartilhada)
        // 53 375 a 381 9(007) Zeros  (vide observação para cobrança compartilhada)
        // 54 382 a 390 9(009) Zeros  (vide observação para cobrança compartilhada)
        // 55 391 a 391 9(001) Indicativo de  Autorização de Liquidação Parcial
        // 56 392 a 392 X(001) Branco
        // 57 393 a 394 9(002) Canal de pagamento do título utilizado pelo sacado/Meio de Apresentação do Título ao Sacado.
        $sequencial_registro = substr($linha, 394, 6); // 58 395 a 400 9(006) Seqüencial do registro

        $this->registros_detalhe[] = array(
            'id' => $i,
            'agencia' => $agencia,
            'agencia_dv' => $agencia_dv,
            'conta' => $conta,
            'conta_dv' => $conta_dv,
            'convenio' => $convenio,
            'nosso_numero' => $nosso_numero,
            'numero' => $numero,
            'tipo_cobranca' => $tipo_cobranca,
            'natureza_recebimento' => $natureza_recebimento,
            'variacao_carteira' => $variacao_carteira,
            'taxa_desconto' => $taxa_desconto,
            'taxa_iof' => $taxa_iof,
            'carteira' => $carteira,
            'data_movimento' => parent::dataMovimento(),
            'data_liquidacao' => $data_liquidacao,
            'data_vencimento' => $data_vencimento,
            'data_credito' => $data_credito,
            'valor_titulo' => $valor_titulo,
            'banco_recebedor' => $banco_recebedor,
            'agencia_recebedor' => $agencia_recebedor,
            'agencia_dv_recebedor' => $agencia_dv_recebedor,
            'especie_titulo' => $especie_titulo,
            'valor_pagto' => $valor_recebido,
            'valor_tarifa' => $valor_tarifa,
            'outras_despesas' => $outras_despesas,
            'juros_desconto' => $juros_desconto,
            'iof_desconto' => $iof_desconto,
            'valor_abatimento' => $valor_abatimento,
            'desconto' => $desconto_concedido,
            'juros_mora' => $juros_mora,
            'outros_recebimentos' => $outros_recebimentos,
            'abatimento_nao_aproveitado' => $abatimento_nao_aproveitado,
            'valor_lancamento' => $valor_lancamento,
            'indicativo_lancamento' => $indicativo_lancamento,
            'indicativo_valor' => $indicativo_valor,
            'valor_ajuste' => $valor_ajuste,
            'sequencial_registro' => $sequencial_registro,
            'tipo_ocorrencia' => $ocorrencia[0],
            'ocorrencia' => $ocorrencia[1]
        );
    }

    private function ocorrencia($comando, $natureza_recebimento)
    {

        $liquidado = ['01' => 'Liquidação normal',
            '02' => 'Liquidação parcial',
            '03' => 'Liquidação por saldo',
            '04' => 'Liquidação com cheque a compensar',
            '05' => 'Liquidação de título sem registro',
            '07' => 'Liquidação na apresentação',
            '09' => 'Liquidação em cartório',
            '10' => 'Liquidação Parcial com Cheque a Compensar',
            '11' => 'Liquidação por Saldo com Cheque a Compensar'];

        $entrada = ['00' => 'por meio magnético',
            '11' => 'por via convencional',
            '16' => 'por alteração do código do cedente',
            '17' => 'por alteração da variação',
            '18' => 'por alteração da carteira'];

        $baixa = ['00' => 'solicitada pelo cliente',
            '15' => 'protestado',
            '18' => 'por alteração da carteira',
            '19' => 'débito automático',
            '31' => 'liquidado anteriormente',
            '32' => 'habilitado em processo',
            '33' => 'incobrável por nosso intermédio',
            '34' => 'transferido para créditos em liquidação',
            '46' => 'por alteração da variação',
            '47' => 'por alteração da variação',
            '51' => 'acerto',
            '90' => 'baixa automática'];

        $recusa = ['01' => 'identificação inválida',
            '02' => 'variação da carteira inválida',
            '03' => 'valor dos juros por um dia inválido',
            '04' => 'valor do desconto inválido',
            '05' => 'espécie de título inválida para carteira/variação',
            '06' => 'espécie de valor invariável inválido',
            '07' => 'prefixo da agência usuária inválido',
            '08' => 'valor do título/apólice inválido',
            '09' => 'data de vencimento inválida',
            '10' => 'fora do prazo/só admissível na carteira',
            '11' => 'inexistência de margem para desconto',
            '12' => 'o banco não tem agência na praça do sacado',
            '13' => 'razões cadastrais',
            '14' => 'sacado interligado com o sacador',
            '15' => 'Titulo sacado contra órgão do Poder Público',
            '16' => 'Titulo preenchido de forma irregular',
            '17' => 'Titulo rasurado',
            '18' => 'Endereço do sacado não localizado ou incompleto',
            '19' => 'Código do cedente inválido',
            '20' => 'Nome/endereço do cliente não informado (ECT)',
            '21' => 'Carteira inválida',
            '22' => 'Quantidade de valor variável inválida',
            '23' => 'Faixa nosso-numero excedida',
            '24' => 'Valor do abatimento inválido',
            '25' => 'Novo número do título dado pelo cedente inválido (Seu número)',
            '26' => 'Valor do IOF de seguro inválido',
            '27' => 'Nome do sacado/cedente inválido',
            '28' => 'Data do novo vencimento inválida',
            '29' => 'Endereço não informado',
            '30' => 'Registro de título já liquidado (carteira 17-tipo 4)',
            '31' => 'Numero do borderô inválido',
            '32' => 'Nome da pessoa autorizada inválido',
            '33' => 'Nosso número já existente',
            '34' => 'Numero da prestação do contrato inválido',
            '35' => 'percentual de desconto inválido',
            '36' => 'Dias para fichamento de protesto inválido',
            '37' => 'Data de emissão do título inválida',
            '38' => 'Data do vencimento anterior à data da emissão do título',
            '39' => 'Comando de alteração indevido para a carteira',
            '40' => 'Tipo de moeda inválido',
            '41' => 'Abatimento não permitido',
            '42' => 'CEP/UF inválido/não compatíveis (ECT)',
            '43' => 'Código de unidade variável incompatível com a data de emissão do título',
            '44' => 'Dados para débito ao sacado inválidos',
            '45' => 'Carteira/variação encerrada',
            '46' => 'Convenio encerrado',
            '47' => 'Titulo tem valor diverso do informado',
            '48' => 'Motivo de baixa invalido para a carteira',
            '49' => 'Abatimento a cancelar não consta do título',
            '50' => 'Comando incompatível com a carteira',
            '51' => 'Código do convenente invalido',
            '52' => 'Abatimento igual ou maior que o valor do titulo',
            '53' => 'Titulo já se encontra na situação pretendida',
            '54' => 'Titulo fora do prazo admitido para a conta 1',
            '55' => 'Novo vencimento fora dos limites da carteira',
            '56' => 'Titulo não pertence ao convenente',
            '57' => 'Variação incompatível com a carteira',
            '58' => 'Impossível a variação única para a carteira indicada',
            '59' => 'Titulo vencido em transferência para a carteira 51',
            '60' => 'Titulo com prazo superior a 179 dias em variação única para carteira 51',
            '61' => 'Titulo já foi fichado para protesto',
            '62' => 'Alteração da situação de débito inválida para o código de responsabilidade',
            '63' => 'DV do nosso número inválido',
            '64' => 'Titulo não passível de débito/baixa – situação anormal',
            '65' => 'Titulo com ordem de não protestar – não pode ser encaminhado a cartório',
            '66' => 'Número do documento do sacado (CNPJ/CPF) inválido',
            '67' => 'Titulo/carne rejeitado',
            '69' => 'Valor/Percentual de Juros Inválido',
            '70' => 'Título já se encontra isento de juros',
            '71' => 'Código de Juros Inválido',
            '72' => 'Prefixo da Ag. cobradora inválido',
            '73' => 'Numero do controle do participante inválido',
            '74' => 'Cliente não cadastrado no CIOPE (Desconto/Vendor)',
            '75' => 'Qtde. de dias do prazo limite p/ recebimento de título vencido inválido',
            '76' => 'Titulo excluído automaticamente por decurso de prazo CIOPE (Desconto/Vendor)',
            '77' => 'Titulo vencido transferido para a conta 1 – Carteira vinculada',
            '84' => 'Título não localizado na existência/Baixado por protesto',
            '80' => 'Nosso numero inválido',
            '81' => 'Data para concessão do desconto inválida',
            '82' => 'CEP do sacado inválido',
            '83' => 'Carteira/variação não localizada no cedente',
            '84' => 'Titulo não localizado na existência',
            '85' => 'Recusa do Comando “41” – Parâmetro de Liquidação Parcial',
            '99' => 'Outros motivos'];

        $alteracaoTipoCobranca = ['00' => 'transferência de título de cobrança simples para descontada ou vice-versa',
            '52' => 'reembolso de título vendor ou descontado, quando ocorrerem reembolsos de títulos por falta de liquidação'];

        switch ($comando) {
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
