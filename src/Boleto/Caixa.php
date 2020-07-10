<?php

namespace Cobranca\Boleto;

use Cobranca\Banco;
use Cobranca\Utils\Functions;
use Cobranca\Utils\Modulo11;

/**
 * Caixa
 */
class Caixa extends AbstractBoleto
{
    public function __construct($valor, $dataVencimento, $numero, $conta, $pagador = null, $sacador = null)
    {
        parent::__construct($valor, $dataVencimento, $numero, $conta, $pagador, $sacador);
    }

    public function banco()
    {
        return Banco::Caixa;
    }

    public function campoLivre()
    {
        $convenio = $this->conta->convenio();
        $dv_convenio = Modulo11::calcular($convenio);
        $campo_livre = Functions::zeros($convenio, 6) .
            Functions::zeros($dv_convenio, 1) .
            substr($this->nossoNumeroSemDV(), 2, 3) .
            substr($this->nossoNumeroSemDV(), 0, 1) .
            substr($this->nossoNumeroSemDV(), 5, 3) .
            substr($this->nossoNumeroSemDV(), 1, 1) .
            substr($this->nossoNumeroSemDV(), 8, 9);
        $dv_campo_livre = Modulo11::calcular($campo_livre);

        return $campo_livre . $dv_campo_livre;
    }

    /**
     * Local de pagamento
     *
     * @return string
     */
    public function localPagamento()
    {
        return 'PREFERENCIALMENTE NAS CASAS LOTÉRICAS ATÉ O VALOR LIMITE';
    }

    /**
     * NossoNumeroCaixa
     * - Número de identificação do título, que permite o Banco e o Beneficiário identificar os dados da cobrança
     *   que deram origem ao boleto.
     * - O Nosso Número no SIGCB é composto de 17 posições, sendo as 02 posições iniciais para identificar a
     *   Carteira e as 15 posições restantes são para livre utilização pelo Beneficiário.
     * - Formato: XYNNNNNNNNNNNNNNN-D, onde:
     *   X                Modalidade/Carteira de Cobrança (1-Registrada/2-Sem Registro)
     *   Y                Emissão do boleto (4-Beneficiário)
     *   NNNNNNNNNNNNNNN  Nosso Número (15 posições livres do Beneficiário)
     *   D                Dígito Verificador do Nosso Número calculado através do Modulo 11, conforme ANEXO IV.
     *                    Admite 0 (zero), diferentemente do DV Geral do Código de Barras.
     */
    public function nossoNumero()
    {
        $dv = Modulo11::calcular($this->nossoNumeroSemDV());
        $dv = strval($dv);

        return $this->nossoNumeroSemDV() . '-' . $dv;
    }

    public function nossoNumeroSemDV()
    {
        $emissao_boleto = '4';
        $modalidade_carteira = $this->conta->carteira();
        // Informar SR para título da modalidade SEM REGISTRO ou RG para título da modalidade REGISTRADA.
        switch ($modalidade_carteira) {
            case 'SR':
                $modalidade_carteira = '2';// 2-Sem Registro
                break;
            case 'RG':
                $modalidade_carteira = '1';// 1-Registrada
                break;
        }
        if (!in_array($modalidade_carteira, ['1', '2'])) {
            throw new \InvalidArgumentException("Modalidade/Carteira de Cobrança inválida", 1);
        }
        $modalidade_carteira = Functions::zeros($modalidade_carteira, 1);

        return $modalidade_carteira . $emissao_boleto . Functions::zeros($this->numero, 15);
    }
}
