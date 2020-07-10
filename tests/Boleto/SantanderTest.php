<?php

namespace Cobranca\Tests\Boleto;

use Cobranca\Conta;
use Cobranca\Pessoa;
use Cobranca\Boleto\Santander;

final class SantanderTest extends \Cobranca\Tests\AbstractTestCase
{
    private $convenio = '0282033';

    public function testDadosBoleto()
    {
        $localPagamento = 'Pagável em qualquer banco até o vencimento.';

        $boleto = new Santander(273.71,
            new \DateTime('2003-05-15'),
            '1984',
            $this->novaConta('101', $this->convenio),
            $this->pagador());
        $this->assertEquals($localPagamento, $boleto->localPagamento());
        $this->assertEquals('033', $boleto->banco());
        $this->assertEquals(date('Y-m-d'), $boleto->dataDocumento());
        $this->assertEquals(date('Y-m-d'), $boleto->dataProcessamento());
        $this->assertEquals('2046', $boleto->fatorVencimento());
        $this->assertEquals('9', $boleto->codigoMoeda());
        $this->assertEquals('R$', $boleto->especieMoeda());
        $this->assertEquals(273.71, $boleto->valor());
        $this->assertEquals('000000001984-4', $boleto->nossoNumero());

        $boleto = new Santander(34.80,
            new \DateTime('2018-06-22'),
            '1984',
            $this->novaConta('101', $this->convenio),
            $this->pagador());
        $this->assertEquals('7563', $boleto->fatorVencimento());
        $this->assertEquals(34.80, $boleto->valor());
        $this->assertEquals('03399028270330000000101984401016275630000003480', $boleto->linhaDigitavel());
    }

    public function testDadosBoletoExemploManual()
    {
        $localPagamento = 'Pagável em qualquer banco até o vencimento.';

        $boleto = new Santander(273.71,
            new \DateTime('2003-05-15'),
            '566612457800',
            $this->novaConta('101', $this->convenio),
            $this->pagador());
        $this->assertEquals($localPagamento, $boleto->localPagamento());
        $this->assertEquals('033', $boleto->banco());
        $this->assertEquals(date('Y-m-d'), $boleto->dataDocumento());
        $this->assertEquals(date('Y-m-d'), $boleto->dataProcessamento());
        $this->assertEquals('2046', $boleto->fatorVencimento());
        $this->assertEquals('9', $boleto->codigoMoeda());
        $this->assertEquals('R$', $boleto->especieMoeda());
        $this->assertEquals(273.71, $boleto->valor());
        $this->assertEquals('566612457800-2', $boleto->nossoNumero());

    }

    public function testCarteiraSemRegistro()
    {
        $localPagamento = 'Pagável em qualquer banco até o vencimento.';

        $boleto = new Santander(11.5,
            new \DateTime('2017-08-10'),
            '701650257',
            $this->novaConta('102', '7041160'),
            $this->pagador());
        $this->assertEquals($localPagamento, $boleto->localPagamento());
        $this->assertEquals('033', $boleto->banco());
        $this->assertEquals(date('Y-m-d'), $boleto->dataDocumento());
        $this->assertEquals(date('Y-m-d'), $boleto->dataProcessamento());
        $this->assertEquals('7247', $boleto->fatorVencimento());
        $this->assertEquals('9', $boleto->codigoMoeda());
        $this->assertEquals('R$', $boleto->especieMoeda());
        $this->assertEquals(11.50, $boleto->valor());
        $this->assertEquals('000701650257-1', $boleto->nossoNumero());
        $this->assertEquals('03399704101600007016550257101027272470000001150', $boleto->LinhaDigitavel());

    }

    /**
    * nova conta
    * params: $carteira, $convenio
    */
    private function novaConta($carteira, $convenio, $agencia = '4042', $conta_corrente = '61900')
    {
        $beneficiario = new Pessoa('Beneficiário', '00000000000191');
        return new Conta('033', $agencia, $conta_corrente, $carteira, '', $convenio, $beneficiario);
    }

    private function pagador()
    {
        return new Pessoa('John Doe', '14443154396');
    }
}
