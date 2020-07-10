<?php

namespace Cobranca\Tests\Boleto;

use Cobranca\Conta;
use Cobranca\Pessoa;
use Cobranca\Boleto\BancoBrasil;

final class BancoBrasilTest extends \Cobranca\Tests\AbstractTestCase
{
    private $numeroBoleto = '7777700168';

    public function testDadosBoleto()
    {
        $localPagamento = 'Pagável em qualquer banco até o vencimento.';

        $bb = new BancoBrasil(135.00,
        new \DateTime('2008-02-02'),
        '7700168',
        $this->novaConta('18', '12387989'),
        $this->pagador());
        $this->assertEquals($localPagamento, $bb->localPagamento());
        $this->assertEquals('001', $bb->banco());
        $this->assertEquals(date('Y-m-d'), $bb->dataDocumento());
        $this->assertEquals(date('Y-m-d'), $bb->dataProcessamento());
        $this->assertEquals('3770', $bb->fatorVencimento());
        $this->assertEquals('9', $bb->codigoMoeda());
        $this->assertEquals('R$', $bb->especieMoeda());
        $this->assertEquals(135.00, $bb->valor());
    }

    public function testBoletoParaConvenio4Digitos()
    {
        $convenio = '1238';

        $bb = new BancoBrasil(135.00,
        new \DateTime('2008-02-01'),
        '123456',
        $this->novaConta('18', $convenio),
        null);
        $this->assertEquals('00191376900000135001238012345640420006190018', $bb->codigoBarras(), 'Código de barras');
        $this->assertEquals('00191238011234564042400061900189137690000013500', $bb->linhaDigitavel(), 'Linhas digitável');
        $this->assertEquals('12380123456-0', $bb->nossoNumero(), 'Nosso número');
        $this->assertEquals('12380123456', $bb->nossoNumeroSemDV(), 'Nosso número sem DV');
        $this->assertEquals('1238012345640420006190018', $bb->campoLivre(), 'Campo livre');
    }

    public function testBoletoParaConvenio6Digitos()
    {
        $convenio = '123879';
            $bb = new BancoBrasil(135.00,
            new \DateTime('2008-02-01'),
            '1234',
            $this->novaConta('18', $convenio),
            $this->pagador());
        $this->assertEquals('00192376900000135001238790123440420006190018', $bb->codigoBarras(), 'Código de barras');
        $this->assertEquals('00191238769012344042300061900189237690000013500', $bb->linhaDigitavel(), 'Linhas digitável');
        $this->assertEquals('12387901234-5', $bb->nossoNumero(), 'Nosso número');
        $this->assertEquals('12387901234', $bb->nossoNumeroSemDV(), 'Nosso número sem DV');
        $this->assertEquals('1238790123440420006190018', $bb->campoLivre(), 'Campo livre');
    }

    public function testBoletoParaConvenio7Digitos()
    {
        $convenio = '1238798';

        $bb = new BancoBrasil(135.00,
            new \DateTime('2008-02-03'),
            '7777700168',
            $this->novaConta('18', $convenio),
            $this->pagador());
        $this->assertEquals('00193377100000135000000001238798777770016818', $bb->codigoBarras(), 'Código de barras');
        $this->assertEquals('12387987777700168-2', $bb->nossoNumero(), 'Nosso número');
        $this->assertEquals('12387987777700168', $bb->nossoNumeroSemDV(), 'Nosso número sem DV');
        $this->assertEquals('0000001238798777770016818', $bb->campoLivre(), 'Campo livre');

        $bb = new BancoBrasil(723.56,
            new \DateTime('2008-02-01'),
            $this->numeroBoleto,
            $this->novaConta('18', $convenio),
            $this->pagador());
        $this->assertEquals('00194376900000723560000001238798777770016818', $bb->codigoBarras(), 'Código de barras');
        $this->assertEquals('00190000090123879877977700168188437690000072356', $bb->linhaDigitavel(), 'Linhas digitável');

        $bb = new BancoBrasil(723.56,
            new \DateTime('2008-02-03'),
            $this->numeroBoleto,
            $this->novaConta('18', $convenio),
            $this->pagador());
        $this->assertEquals('00195377100000723560000001238798777770016818', $bb->codigoBarras(), 'Código de barras');
        $this->assertEquals('00190000090123879877977700168188537710000072356', $bb->linhaDigitavel(), 'Linhas digitável');

        $bb = new BancoBrasil(1000.00,
            new \DateTime('2016-08-12'),
            '000000000000098468214',
            $this->novaConta('17', '1559984'),
            $this->pagador());
        $this->assertEquals('00190000090155998400898468214170168840000100000', $bb->linhaDigitavel(), "Linha digitável inválida, cart 17, número maior");

        $bb = new BancoBrasil(1000.00,
            new \DateTime('2016-08-12'),
            '98468214',
            $this->novaConta('17', '1559984'),
            $this->pagador());
        $this->assertEquals('00190000090155998400898468214170168840000100000', $bb->linhaDigitavel(), "Linha digitável inválida, cart 17, número menor");

        $bb = new BancoBrasil(5.45,
            new \DateTime('2017-03-08'),
            '2',
            $this->novaConta('17', '2962087', '3680', '15399'),
            $this->pagador());
        $this->assertEquals('00194709200000005450000002962087000000000217', $bb->codigoBarras(), "Código de barras inválid8, número 2");
        $this->assertEquals('00190000090296208700900000002170470920000000545', $bb->linhaDigitavel(), "Linha digitável inválida, cart 17, número 2");
    }

    public function testBoletoParaConvenio8Digitos()
    {
        $convenio = '12387989';

        $bb = new BancoBrasil(135.00,
            new \DateTime('2008-02-02'),
            '7700168',
            $this->novaConta('18', $convenio),
            $this->pagador());
        $this->assertEquals('00193377000000135000000001238798900770016818', $bb->codigoBarras(), "Código de barras inválido, número 7700168");
        $this->assertEquals('00190000090123879890207700168185337700000013500', $bb->linhaDigitavel(), "Linha digitável inválida, número 7700168");
        $this->assertEquals('12387989007700168-7', $bb->nossoNumero(), 'Nosso número');
        $this->assertEquals('12387989007700168', $bb->nossoNumeroSemDV(), 'Nosso número sem DV');
        $this->assertEquals('0000001238798900770016818', $bb->campoLivre(), 'Campo livre');

        $bb = new BancoBrasil(135.00,
            new \DateTime('2008-02-01'),
            $this->numeroBoleto,
            $this->novaConta('18', $convenio),
            $this->pagador());
        $this->assertEquals('00193376900000135000000001238798977770016818', $bb->codigoBarras(), "Código de barras inválido, número 777700168");
        $this->assertEquals('00190000090123879897777700168188337690000013500', $bb->linhaDigitavel(), "Linha digitável inválida, número 777700168");
    }

    /**
     * @expectedException InvalidArgumentException
     */
    public function testDeveGerarExcecaoParaConvenioInvalido()
    {
        $convenio = '1234567890123';
        $bb = new BancoBrasil(135.00,
            new \DateTime('2008-02-02'),
            '7700168',
            $this->novaConta('18', $convenio),
            $this->pagador());
        $bb->nossoNumero();
    }

    /**
     * @expectedException InvalidArgumentException
     */
    public function testDeveGerarExcecaoAoGerarCampoLivreComConvenioInvalido()
    {
        $convenio = '1234567890123';
        $bb = new BancoBrasil(135.00,
            new \DateTime('2008-02-02'),
            '7700168',
            $this->novaConta('18', $convenio),
            $this->pagador());
        $bb->campoLivre();
    }

    /**
    * nova conta
    * params: $carteira, $convenio
    */
    private function novaConta($carteira, $convenio, $agencia = '4042', $conta_corrente = '61900')
    {
        $beneficiario = new Pessoa('Beneficiário', '00000000000191');

        return new Conta('001', $agencia, $conta_corrente, $carteira, '', $convenio, $beneficiario);
    }

    private function pagador()
    {
        return new Pessoa('John Doe', '14443154396');
    }
}
