<?php

namespace Cobranca\Boleto;

use Cobranca\Banco;
use Cobranca\Utils\Functions;
use Cobranca\Utils\Modulo11;

/**
 * Santander
 */
class Santander extends AbstractBoleto
{
    // IOF – Seguradoras (Se 7% informar 7. Limitado a 9%)
    // Demais clientes usar 0 (zero)
    private $iof = '0';

    public function __construct($valor, $dataVencimento, $numero, $conta, $pagador = null, $sacador = null)
    {
        parent::__construct($valor, $dataVencimento, $numero, $conta, $pagador, $sacador);
    }

    public function banco()
    {
        return Banco::Santander;
    }

    public function campoLivre()
    {
        $convenio = Functions::zeros($this->conta->convenio(), 7);
        $carteira = Functions::zeros($this->conta->carteira(), 3);
        $nossoNumero = Functions::onlyNumbers($this->nossoNumero());
        return '9' . $convenio . $nossoNumero . '0' . $carteira;
    }

    /**
    * Local de pagamento
    *
    * @return string
    */
    public function localPagamento()
    {
        return 'Pagável em qualquer banco até o vencimento.';
    }

    /**
     * Nosso Número
     * Código de controle que permite ao Banco e ao beneficiário identificar os dados da cobrança que deu origem ao boleto de pagamento.
     *
     * @return string
     */
    public function nossoNumero()
    {
        $dv = Modulo11::calcular($this->nossoNumeroSemDV());
        $dv = strval($dv);
        $dv = (strlen($dv) == 1) ? '-' . $dv : '';
        return $this->nossoNumeroSemDV() . $dv;
    }

    /**
     * Nosso Número sem o DV (Dígito Verificador)
     *
     * @return string
     */
    public function nossoNumeroSemDV()
    {
        return str_pad($this->numero, 12, '0', STR_PAD_LEFT);
    }
}
