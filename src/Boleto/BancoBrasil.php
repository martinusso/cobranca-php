<?php

namespace Cobranca\Boleto;

use Cobranca\Banco;
use Cobranca\Utils\Functions;
use Cobranca\Utils\Modulo11;

/**
 * Banco do Brasil
 */
class BancoBrasil extends AbstractBoleto
{
    public function __construct($valor, $dataVencimento, $numero, $conta, $pagador = null, $sacador = null)
    {
        parent::__construct($valor, $dataVencimento, $numero, $conta, $pagador, $sacador);
    }

    public function banco()
    {
        return Banco::BancoDoBrasil;
    }

    public function campoLivre()
    {
        $carteira = Functions::zeros($this->conta->carteira(), 2);
        switch (strlen($this->conta->convenio())) {
            case 4;
            case 6:
                return Functions::zeros($this->nossoNumeroSemDV(), 11) .
                str_pad($this->conta->agencia(), 4, '0', STR_PAD_LEFT) .
                str_pad($this->conta->contaCorrente(), 8, '0', STR_PAD_LEFT) .
                $carteira;
            case 7;
            case 8:
                return '000000' . Functions::zeros($this->nossoNumeroSemDV(), 17) . $carteira;
            default:
                throw new \InvalidArgumentException('Tipo de convênio não implementado.');
        }
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
        $dv = Modulo11::calcular($this->nossoNumeroSemDV(), array(10 => 'X'));
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
        $convenio = $this->conta->convenio();
        switch (strlen($convenio)) {
            case 4:
                $quantidade = 7;
                break;
            case 7:
                $quantidade = 10;
                break;
            case 6:
                $quantidade = 5;
                break;
            case 8:
                $quantidade = 9;
                break;
            default:
                throw new \InvalidArgumentException('Tipo de convênio não implementado.');
        }
        $ultimosDigitos = substr($this->numero, $quantidade*-1);
        $numero = str_pad($ultimosDigitos, $quantidade, '0', STR_PAD_LEFT);

        return $convenio . $numero;
    }
}
