<?php

namespace Cobranca\Tests\Boleto;

use Cobranca\Conta;
use Cobranca\Pessoa;
use Cobranca\Boleto\Caixa;

final class CaixaTest extends \Cobranca\Tests\AbstractTestCase
{
    public function testBoleto()
    {
        $localPagamento = 'PREFERENCIALMENTE NAS CASAS LOTÉRICAS ATÉ O VALOR LIMITE';
        $convenio = '245274';

        $caixa = new Caixa(10,
            new \DateTime('2008-02-01'),
            '1',
            $this->novaConta('1', $convenio),
            null);
        $this->assertEquals($localPagamento, $caixa->localPagamento(), 'Local Pagamento');
        $this->assertEquals('14000000000000001-4', $caixa->nossoNumero(), 'Nosso número');
        $this->assertEquals('10493376900000010002452741000100040000000014', $caixa->codigoBarras(), 'Código de barras');
        $this->assertEquals('10492452754100010004400000000141337690000001000', $caixa->linhaDigitavel(), 'Linhas digitável');
    }

    public function testBoletoSR()
    {
        $convenio = '001761';

        $caixa = new Caixa(100,
            new \DateTime('2014-10-04'),
            '901864102',
            $this->novaConta('SR', $convenio),
            null);
        $this->assertEquals('24000000901864102-1', $caixa->nossoNumero(), 'Nosso número');
        $this->assertEquals('10490017691200020004390186410230462060000010000', $caixa->linhaDigitavel(), 'Linhas digitável');

        $caixa = new Caixa(100,
            new \DateTime('2014-10-04'),
            '901864102',
            $this->novaConta('2', $convenio),
            null);
        $this->assertEquals('24000000901864102-1', $caixa->nossoNumero(), 'Nosso número');
        $this->assertEquals('10490017691200020004390186410230462060000010000', $caixa->linhaDigitavel(), 'Linhas digitável');
    }

    public function testBoletoRG()
    {
        $convenio = '001761';

        $caixa = new Caixa(100,
            new \DateTime('2014-10-04'),
            '901864102',
            $this->novaConta('RG', $convenio),
            null);
        $this->assertEquals('14000000901864102-3', $caixa->nossoNumero(), 'Nosso número');
        $this->assertEquals('10490017691200010004590186410214562060000010000', $caixa->linhaDigitavel(), 'Linhas digitável');

        $caixa = new Caixa(100,
            new \DateTime('2014-10-04'),
            '901864102',
            $this->novaConta('1', $convenio),
            null);
        $this->assertEquals('14000000901864102-3', $caixa->nossoNumero(), 'Nosso número');
        $this->assertEquals('10490017691200010004590186410214562060000010000', $caixa->linhaDigitavel(), 'Linhas digitável');
    }

    /**
     * @expectedException InvalidArgumentException
     */
    public function testDeveGerarExcecaoParaCarteiraInvalida()
    {
        $convenio = '1234567890123';

        $caixa = new Caixa(100,
            new \DateTime('2014-10-04'),
            '901864102',
            $this->novaConta('0', $convenio),
            null);
        $caixa->nossoNumeroSemDV();
    }

    private function novaConta($carteira, $convenio, $agencia = '1969', $conta_corrente = '0000528')
    {
        $beneficiario = new Pessoa('ACME Corporation', '04092706000181');

        return new Conta('104', $agencia, $conta_corrente, $carteira, '', $convenio, $beneficiario);
    }

    private function pagador()
    {
        return new Pessoa('John Doe', '14443154396');
    }
}
