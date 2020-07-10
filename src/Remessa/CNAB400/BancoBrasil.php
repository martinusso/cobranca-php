<?php

namespace Cobranca\Remessa\CNAB400;

use Cobranca\Utils\Functions;
use Cobranca\Utils\Modulo11;
use Cobranca\Boleto\BancoBrasil as BoletoBancoBrasil;

/**
 * Remessa do Banco do Brasil
 */
class BancoBrasil extends AbstractBase
{
    protected function getHeader()
    {
        return '0' . // 01.0 001 a 001 9(001) Identificação do Registro Header: “0” (zero)
            '1' . // 02.0 002 a 002 9(001) Tipo de Operação: “1” (um)
            'REMESSA' . // 03.0 003 a 009 X(007) Identificação por Extenso do Tipo de Operação
            '01' . // 04.0 010 a 011 9(002) Identificação do Tipo de Serviço: “01”
            'COBRANCA' . // 05.0 012 a 019 X(008) Identificação por Extenso do Tipo de Serviço: “COBRANCA”
            Functions::brancos('', 7) . // 06.0 020 a 026 X(007) Complemento do Registro: “Brancos”
            Functions::zeros($this->agencia, 4) . // 07.0 027 a 030 9(004) Prefixo da Agência: Número da Agência onde está cadastrado o convênio líder do cedente
            Functions::zeros($this->agencia_dv, 1) . // 08.0 031 a 031 X(001) Dígito Verificador - D.V. - do Prefixo da Agência
            Functions::zeros($this->conta_numero, 8) . // 09.0 032 a 039 9(008) Número da Conta Corrente: Número da conta onde está cadastrado o Convênio Líder do Cedente
            Functions::zeros($this->conta_dv, 1) . // 10.0 040 a 040 X(001) Dígito Verificador - D.V. – do Número da Conta Corrente do Cedente
            Functions::zeros('0', 6) . // 11.0 041 a 046 9(006) Complemento do Registro: “000000”
            Functions::brancos($this->nome_cedente, 30) . // 12.0 047 a 076 X(030) Nome do Cedente
            '001BANCODOBRASIL  ' . // 13.0 077 a 094 X(018) 001BANCODOBRASIL
            date('dmy') . // 14.0 095 a 100 9(006) Data da Gravação: Informe no formato “DDMMAA”
            Functions::zeros($this->sequencial_remessa, 7) . // 15.0 101 a 107 9(007) Seqüencial da Remessa
            Functions::brancos('', 22) . // 16.0 108 a 129 X(22) Complemento do Registro: “Brancos”
            Functions::zeros($this->convenio, 7) . // 17.0 130 a 136 9(007) Número do Convênio Líder (numeração acima de 1.000.000 um milhão)
            Functions::brancos('', 258) . // 18.0 137 a 394 X(258) Complemento do Registro: “Brancos”
            '000001' // 19.0 395 a 400 9(006) Seqüencial do Registro: ”000001”
            ;
    }

    protected function getTrailler()
    {
        return '9' . // 01.9 001 a 001 9(001) Identificação do Registro Trailer: “9”
            Functions::brancos('', 393) . // 02.9 002 a 394 X(393) Complemento do Registro: “Brancos”
            Functions::zeros($this->sequencial_registro+1, 6) // 03.9 395 a 400 9(006) Número Seqüencial do Registro no Arquivo
            ;
    }

    protected function getPagamentos()
    {
        $this->sequencial_registro = 1;

        $return = array();
        foreach ($this->pagamentos as $key => $pagamento) {
            $this->sequencial_registro += 1;
            $return[] =
                '7' .// 01.7 001 a 001 9(001) Identificação do Registro Detalhe: 7 (sete)
                $this->conta->beneficiario()->tipoInscricao() . // 02.7 002 a 003 9(002) Tipo de Inscrição do Cedente
                Functions::zeros($this->conta->beneficiario()->documento(), 14) . // 03.7 004 a 017 9(014) Número do CPF/CNPJ do Cedente
                Functions::zeros($this->agencia, 4) . // 04.7 018 a 021 9(004) Prefixo da Agência
                Functions::zeros($this->agencia_dv, 1) . // 05.7 022 a 022 X(001) Dígito Verificador - D.V. - do Prefixo da Agência
                Functions::zeros($this->conta_numero, 8) . // 06.7 023 a 030 9(008) Número da Conta Corrente do Cedente
                Functions::zeros($this->conta_dv, 1) . // 07.7 031 a 031 X(001) Dígito Verificador - D.V. - do Número da Conta Corrente do Cedente
                Functions::zeros($this->convenio, 7) . // 08.7 032 a 038 9(007) Número do Convênio de Cobrança do Cedente
                Functions::brancos('', 25) . // 09.7 039 a 063 X(025) Código de Controle da Empresa
                $this->nossoNumero($pagamento->valor(), $pagamento->dataVencimento(), $pagamento->numero()) . // 10.7 064 a 080 9(017) Nosso-Número
                '00' . // 11.7 081 a 082 9(002) Número da Prestação: “00” (Zeros)
                '00' . // 12.7 083 a 084 9(002) Grupo de Valor: “00” (Zeros)
                '   ' . // 13.7 085 a 087 X(003) Complemento do Registro: “Brancos”
                $this->indicativoMensagem($pagamento) . // 14.7 088 a 088 X(001) Indicativo de Mensagem ou Sacador/Avalista
                '   ' . // 15.7 089 a 091 X(003) Prefixo do Título: “Brancos”
                Functions::zeros($this->variacao, 3) . // 16.7 092 a 094 9(003) Variação da Carteira
                '0' . // 17.7 095 a 095 9(001) Conta Caução: “0” (Zero)
                '000000' . // 18.7 096 a 101 9(006) Número do Borderô: “000000” (Zeros)
                $this->tipoCobranca($pagamento) . // 19.7 102 a 106 X(005) Tipo de Cobrança
                Functions::zeros($this->carteira, 2) . // 20.7 107 a 108 9(002) Carteira de Cobrança
                $this->comando($pagamento->comando()) . // 21.7 109 a 110 9(002) Comando
                Functions::zeros($pagamento->numero(), 10) . // 22.7 111 a 120 X(010) Seu Número/Número do Título Atribuído pelo Cedente
                $this->dataVencimento($pagamento) . // 23.7 121 a 126 9(006) Data de Vencimento
                $this->valorFormatado($pagamento->valor()) . // 24.7 127 a 139 9(011)v99 Valor do Título
                '001' . // 25.7 140 a 142 9(003) Número do Banco: “001”
                '0000' . // 26.7 143 a 146 9(004) Prefixo da Agência Cobradora: “0000”
                ' ' . // 27.7 147 a 147 X(001) Dígito Verificador do Prefixo da Agência Cobradora: “Brancos”
                Functions::zeros($this->conta->especieTitulo(), 2) . // 28.7 148 a 149 9(002) Espécie de Titulo
                Functions::brancos($this->aceite(), 1) . // 29.7 150 a 150 X(001) Aceite do Título
                date('dmy') . // 30.7 151 a 156 9(006) Data de Emissão: Informe no formato “DDMMAA”
                $this->instrucaoCodificada($pagamento->comando()) . // 31.7 157 a 158 9(002) Instrução Codificada
                '00' . // 32.7 159 a 160 9(002) Instrução Codificada
                $this->calcularJurosMoraPorDiaAtraso($pagamento) . // 33.7 161 a 173 9(011)v99 Juros de Mora por Dia de Atraso
                '000000' . // 34.7 174 a 179 9(006) Data Limite para Concessão de Desconto/Data de Operação do BBVendor/Juros de Mora
                $this->valorFormatado(0) . // 35.7 180 a 192 9(011)v99 Valor do Desconto
                $this->valorFormatado(0) . // 36.7 193 a 205 9(011)v99 Valor do IOF/Qtde Unidade Variável
                $this->valorFormatado(0) . // 37.7 206 a 218 9(011)v99 Valor do Abatimento
                $pagamento->pagador()->tipoInscricao() . // 38.7 219 a 220 9(002) Tipo de Inscrição do Sacado
                Functions::zeros($pagamento->pagador()->documento(), 14) . // 39.7 221 a 234 9(014) Número do CNPJ ou CPF do Sacado
                Functions::brancos($pagamento->pagador()->nome(), 37) . // 40.7 235 a 271 X(037) Nome do Sacado
                '   ' . // 41.7 272 a 274 X(003) Complemento do Registro: “Brancos”
                Functions::brancos($pagamento->pagador()->endereco(), 40) . // 42.7 275 a 314 X(040) Endereço do Sacado
                Functions::brancos($pagamento->pagador()->bairro(), 12) . // 43.7 315 a 326 X(012) Bairro do Sacado
                Functions::brancos($pagamento->pagador()->cep(), 8) . // 44.7 327 a 334 9(008) CEP do Endereço do Sacado
                Functions::brancos($pagamento->pagador()->cidade(), 15) . // 45.7 335 a 349 X(015) Cidade do Sacado
                Functions::brancos($pagamento->pagador()->uf(), 2) . // 46.7 350 a 351 X(002) UF da Cidade do Sacado
                Functions::brancos($pagamento->mensagemAvalista(), 40) . // 47.7 352 a 391 X(040) Observações/Mensagem ou Sacador/Avalista
                Functions::zeros($pagamento->diasProtesto(), 2) . // 48.7 392 a 393 X(002) Número de Dias Para Protesto
                ' ' . // 49.7 394 a 394 X(001) Complemento do Registro: “Brancos”
                Functions::zeros($this->sequencial_registro, 6) // 50.7 395 a 400 9(006) Seqüencial de Registro
            ;

            if (!$this->isComandoCancelamento($pagamento->comando())) {
                $registroMulta = $this->registroMulta($pagamento, $this->sequencial_registro+1);
                if (!empty($registroMulta)) {
                    $this->sequencial_registro += 1;
                    $return[] = $registroMulta;
                }
            }

        }
        return $return;
    }

    private function comando($comando)
    {
        switch ($comando) {
            case 'cancelamento': // 02 - Solicitação de baixa
                return '02';
            default:
                return '01';
        }
    }

    private function isComandoCancelamento($comando)
    {
        return ($comando == 'cancelamento');
    }

    private function instrucaoCodificada($comando)
    {
        switch ($comando) {
            case 'cancelamento': // 44 – Baixar
                return '44';
            default:
                return '00';
        }
    }

    private function registroMulta($pagamento, $sequencial)
    {
        $multa = $pagamento->percentualMulta();
        if (empty($multa)) return '';
        if ($multa <= 0) return '';

        return '5' . // 01. 5 001 a 001 9(001) Identificação do Registro Transação: “5”
            '99' . // 02. 5 002 a 003 X(002) Tipo de Serviço: “99” (Cobrança de Multa)
            '2' . // 03. 5 004 a 004 9(001) Código de Multa ['1' = Valor/'2' = Percentual]
            $this->dataVencimento($pagamento) . // 04. 5 005 a 010 9(006) Data de Inicio da Cobrança da Multa
            $this->valorFormatado($multa, 10) . // 05. 5 011 a 022 9(012) Valor/Percentual da Multa
            Functions::brancos('', 372) . // 06. 5 023 a 394 9(372) Complemento do Registro: “Brancos”
            Functions::zeros($sequencial, 6) // 50.7 395 a 400 9(006) Seqüencial de Registro
            ;
    }

    protected function digitoAgencia()
    {
        return Modulo11::calcular($this->agencia, array(10 => 'X'));
    }

    protected function digitoContaCorrente()
    {
        return Modulo11::calcular($this->conta_numero, array(10 => 'X'));
    }

    protected function nossoNumero($valor, $dataVencimento, $numero)
    {
        $bb = new BoletoBancoBrasil($valor, $dataVencimento, $numero, $this->conta, null);
        return Functions::zeros($bb->nossoNumeroSemDV(), 17);
    }

    /**
     * Aceite do título
     * N - Sem aceite
     * A - Com aceite - Indica o reconhecimento formal (assinatura no documento) do sacado no título.
     *
     * @return string
     */
    private function aceite()
    {
        if ($this->conta->aceite() == 'N') {
            return 'N';
        }
        return 'A';
    }

    /**
    * Indicativo de mensagem ou Sacador Avalista
    *
    * a) “Brancos”: Poderá ser informada nas posições 352 a 391 qualquer mensagem para ser impressa no boleto.
    * b) “A”: Deverá ser informado nas posições 352 a 391 o nome e CPF/CNPJ do sacador (emitente original do título), de acordo com o contido no item “c”
    * Observações:
    *  I)   Quando o campo  for preenchido com “Brancos”, as informações indicadas nas posições 352 a 391 serão impressas
    *       no campo “texto de responsabilidade do Cedente” na Ficha de Compensação do boleto de cobrança.
    *  II)  Não informar dados conflitantes com as informações dos demais campos, como juros, IOF, desconto, protesto, etc.
    *  III) Os dados informados neste campo como “mensagens” não são impressos na segunda via do boleto de cobrança emitida
    *       por meio da Internet, Gerenciador Financeiro ou Agência do Banco.
    * c) Quando o campo 14.7 – Indicativo de Mensagem ou Sacador/Avalista - for preenchido “A” , os campos 352 a 391 deverão ser preenchidos da seguinte maneira:
    *    Para CNPJ
    *      Posição 352 à 372 - Preencher com o nome do Sacador/Avalista.
    *      Posição 373 - Preencher com "espaço"
    *      Posição 374 à 377 - Preencher com o literal "CNPJ"
    *      Posição 378 à 391 - Preencher com o número do CNPJ do Sacador/Avalista
    *    Para CPF
    *      Posição 352 à 376 - Preencher com o nome do Sacador/Avalista.
    *      Posição 377 - Preencher com "espaço"
    *      Posição 378 à 380 - Preencher com o literal "CPF"
    *      Posição 381 à 391 - Preencher com o número do CPF do Sacador/Avalista
    * Observações:  Os dados do sacador/avalista serão impressos no campo “SACADOR/AVALISTA” do boleto de cobrança – Recibo do Sacado e Ficha de Compensação.
    *  Este dado é impresso quando da emissão de segunda via do boleto na agência, Internet ou Gerenciador Financeiro
    *
    * @param $pagamento
    * @return string Retorna 'A' quando pagamento possui Sacador/Avalista, ou ' ' quando não
    */
    private function indicativoMensagem($pagamento)
    {
        $avalista = $pagamento->avalista();
        $possui_avalista = isset($avalista['nome']) && isset($avalista['documento']);
        return ($possui_avalista) ? 'A' : ' ';
    }

    /**
    * Tipo de Cobrança
    * a) Carteiras 11 ou 17:
    *    - 04DSC: Solicitação de registro na Modalidade Descontada
    *    - 08VDR: Solicitação de registro na Modalidade BBVendor
    *    - 02VIN: solicitação de registro na Modalidade Vinculada
    *    - BRANCOS: Registro na Modalidade Simples
    * b) Carteiras 12, 31, 51:
    *    - Brancos
    *
    * @param $pagamento
    * @return string
    */
    private function tipoCobranca($pagamento)
    {
        switch ($this->carteira) {
            case '11';
            case '17':
                return '     ';
            default:
                return '     ';
        }
    }

    /**
    * Data de vencimento
    * Informe a data de vencimento do título no formando “DDMMAA”, onde:
    *   DD = Dia
    *   MM = Mês
    *   AA = Ano
    * ou;
    *   -  888888: Para vencimento “À Vista”
    *   -  999999: Para vencimento “Na Apresentação”
    * Nos casos de indicação de vencimento “A Vista ou Na Apresentação” o vencimento ocorrerá 15 dias após a data do registro no Banco.
    * Observações
    * a) Carteiras 11, 12, 15, 17 e 31:
    *   I  - Admite o registro de títulos com prazo de vencimento até 2.500 dias
    * b) Carteira 51:
    *   I – Admite o registro de títulos com prazo de vencimento de até 180 dias
    * c) O Sistema aceita o registro de títulos vencidos nas carteiras 11, 12, 15, 17 e 31 até um dia útil anterior ao prazo de Baixa Automática cadastrado no Sistema de Cobrança do Banco.
    * d) Não é admitido o registro de título vencido nas Modalidades de Cobrança Desconto e Vendor.
    *
    * @param $pagamento
    * @return string
    */
    private function dataVencimento($pagamento)
    {
        return $pagamento->dataVencimento()->format('dmy');
    }

    public function erro()
    {
        return '';
    }
}
