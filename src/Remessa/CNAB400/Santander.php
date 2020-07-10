<?php

namespace Cobranca\Remessa\CNAB400;

use Cobranca\Utils\Functions;

/**
 * Remessa do Santander
 */
class Santander extends AbstractBase
{
    private $quantidadeDocumentos = 0;
    private $valorTotalDocumentos = 0;

    protected function getHeader()
    {
        return
            '0' . // Código do registro = 0
            '1' . // Código da remessa = 1
            'REMESSA' . // Literal de transmissão = REMESSA
            '01' . // Código do serviço = 01
            Functions::brancos('COBRANCA', 15) . // Literal de serviço = COBRANÇA
            Functions::zeros($this->codigoTransmissao(), 20) . // Código de Transmissão (nota 1)
            Functions::brancos($this->nome_cedente, 30) . // Nome do Beneficiário
            '033' . // Código do Banco = 353/033
            'SANTANDER      ' . // Nome do Banco = SANTANDER
            date('dmy') . // Data de Gravação
            Functions::zeros(0, 16) . // Zeros
            Functions::brancos('', 47) . // Mensagem 1
            Functions::brancos('', 47) . // Mensagem 2
            Functions::brancos('', 47) . // Mensagem 3
            Functions::brancos('', 47) . // Mensagem 4
            Functions::brancos('', 47) . // Mensagem 5
            Functions::brancos('', 34) . // Brancos
            Functions::brancos('', 6) . // Brancos
            Functions::zeros(0, 3) . // Número da versão da remessa opcional, se informada, será controlada pelo sistema
            '000001' // Número sequencial do registro no arquivo = 000001
            ;
    }

    protected function getTrailler()
    {
        return
            '9' . // Código do registro = 9
            Functions::zeros($this->quantidadeDocumentos, 6) .
            $this->valorFormatado($this->valorTotalDocumentos) .
            Functions::zeros(0, 374) .
            Functions::zeros($this->sequencial_registro+1, 6);
    }

    protected function getPagamentos()
    {
        $this->sequencial_registro = 1;
        $this->quantidadeDocumentos = 0;
        $this->valorTotalDocumentos = 0;

        $return = array();
        foreach ($this->pagamentos as $key => $pagamento) {
            $indMulta = ($pagamento->percentualMulta()) ? '4' : '0';
            $this->quantidadeDocumentos += 1;
            $this->valorTotalDocumentos += $pagamento->valor();
            $this->sequencial_registro += 1;

            $return[] =
                '1' .
                $this->conta->beneficiario()->tipoInscricao() .
                Functions::zeros($this->conta->beneficiario()->documento(), 14) .
                Functions::zeros($this->agencia, 4) .
                Functions::zeros($this->convenio, 8) .
                Functions::zeros($this->conta_numero, 8, true, false) .
                Functions::brancos('', 25) .
                Functions::zeros($pagamento->nossoNumero(), 8) .
                '000000' . // Data do segundo desconto
                ' ' .
                $indMulta . // Informação de multa = 4, senão houver informar zero Verificar página 16
                $this->valorFormatado($pagamento->percentualMulta(), 2) .
                '00' . // Unidade de valor moeda corrente = 00
                Functions::zeros(0, 13) . // Valor do título em outra unidade (consultar banco)
                '    ' .
                '000000' . // Data para cobrança de multa. (Nota 4)
                '5' . // Código da carteira
                $this->comando($pagamento->comando()) . // Código da ocorrência
                Functions::zeros($pagamento->numero(), 10) . // Seu número
                $pagamento->dataVencimento()->format('dmy') .
                $this->valorFormatado($pagamento->valor()) . // 24.7 127 a 139 9(011)v99 Valor do Título
                '033' .
                '00000' . // Código da agência cobradora do Banco Santander informar somente se carteira for igual a 5, caso contrário, informar zeros.
                Functions::zeros($this->conta->especieTitulo(), 2) . // Espécie de documento
                Functions::brancos($this->aceite(), 1) .
                date('dmy')  . // Data da emissão do título
                '00' . // Primeira instrução cobrança
                '00' . // Segunda instrução cobrança
                $this->calcularJurosMoraPorDiaAtraso($pagamento) . // Valor de mora a ser cobrado por dia de atraso
                '000000' . // Data limite para concessão de desconto
                $this->valorFormatado(0) . // Valor de desconto a ser concedido
                $this->valorFormatado(0, 8, 5) . // Valor do IOF a ser recolhido pelo Banco para nota de seguro
                $this->valorFormatado(0) . // Valor do abatimento a ser concedido ou valor do segundo desconto. Vide posição 71.
                $pagamento->pagador()->tipoInscricao() .
                Functions::zeros($pagamento->pagador()->documento(), 14) .
                Functions::brancos($pagamento->pagador()->nome(), 40) .
                Functions::brancos($pagamento->pagador()->endereco(), 40) .
                Functions::brancos($pagamento->pagador()->bairro(), 12) .
                Functions::brancos($pagamento->pagador()->cep(), 8) .
                Functions::brancos($pagamento->pagador()->cidade(), 15) .
                Functions::brancos($pagamento->pagador()->uf(), 2) .
                Functions::brancos('', 30) . // Nome do sacador ou coobrigado
                ' ' .
                $this->complemento() .
                Functions::brancos('', 6) .
                '00' .
                ' ' .
                Functions::zeros($this->sequencial_registro, 6) // Sequencial de Registro
            ;
        }
        return $return;
    }

    private function contaPadraoNovo()
    {
        return strlen($this->conta_numero) > 8;
    }

    private function codigoTransmissao()
    {
        return isset($this->params['codigo_transmissao']) ? $this->params['codigo_transmissao'] : null;
    }

    private function complemento()
    {
        if ($this->contaPadraoNovo()) {
            return 'I' .
                Functions::zeros(substr($this->conta_numero, -1), 1) .
                Functions::zeros($this->conta_dv, 1);
        }
        return '   ';
    }

    private function comando($comando)
    {
        // Código da ocorrência:
        // 01 = ENTRADA DE TÍTULO
        // 02 = BAIXA DE TÍTULO
        // 04 = CONCESSÃO DE ABATIMENTO
        // 05 = CANCELAMENTO ABATIMENTO
        // 06 = ALTERAÇÃO DE VENCIMENTO
        // 07 = ALT. NÚMERO CONT.BENEFICIÁRIO
        // 08 = ALTERAÇÃO DO SEU NÚMERO
        // 09 = PROTESTAR
        // 18 = SUSTAR PROTESTO (Após início do ciclo de protesto)
        // 98 = NÃO PROTESTAR (Antes do início do ciclo de protesto)
        switch ($comando) {
            case 'cancelamento': // 02 - Solicitação de baixa
                return '02';
            default:
                return '01';
        }
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

    private function dataVencimento($pagamento)
    {
        return $pagamento->dataVencimento()->format('dmy');
    }

    public function erro()
    {
        return '';
    }
}
