<?php

namespace Cobranca\Tests\Boleto;

use Cobranca\Utils\LinhaDigitavel;

final class LinhaDigitavelTest extends \Cobranca\Tests\AbstractTestCase
{
    public function testLinhaDigitavel()
    {
        $linha_digitavel = LinhaDigitavel::create('00195709300000545330000002962087000000000217');
        $this->assertEquals('00190000090296208700900000002170570930000054533', $linha_digitavel, '1');

        $linha_digitavel = LinhaDigitavel::create('00193000000000005450000002962087000000000217');
        $this->assertEquals('00190000090296208700900000002170300000000000545', $linha_digitavel, '2');

        $linha_digitavel = LinhaDigitavel::create('00197000000000005450000002962087000000000117');
        $this->assertEquals('00190000090296208700900000001172700000000000545', $linha_digitavel, 'boleto número 1');

        $linha_digitavel = LinhaDigitavel::create('00199000000000042090000002962087000000000217');
        $this->assertEquals('00190000090296208700900000002170900000000004209', $linha_digitavel, 'boleto número 2');

        $linha_digitavel = LinhaDigitavel::create('00198000000000176720000002962087000000000317');
        $this->assertEquals('00190000090296208700900000003178800000000017672', $linha_digitavel, 'boleto número 3');

        $linha_digitavel = LinhaDigitavel::create('00199000000001540360000002962087000000000417');
        $this->assertEquals('00190000090296208700900000004176900000000154036', $linha_digitavel, 'boleto número 4');

        $linha_digitavel = LinhaDigitavel::create('00196000000017852930000002962087000000000517');
        $this->assertEquals('00190000090296208700900000005173600000001785293', $linha_digitavel, 'boleto número 5');

        $linha_digitavel = LinhaDigitavel::create('03394740000012345679897033512345678901230101');
        $this->assertEquals('03399897093351234567089012301019474000001234567', $linha_digitavel, 'boleto número 6');
    }

    public function testLerLinhaDigitavelBancoDoBrasil()
    {
        $linhaDigitavel = LinhaDigitavel::ler('00190000090296208700900000017178271570000020000');
        $this->assertEquals('001', $linhaDigitavel['codigo_banco'], 'Código do banco');
        $this->assertEquals('9', $linhaDigitavel['moeda'], 'Moeda');
        $this->assertEquals('7157', $linhaDigitavel['fator_vencimento'], 'Fator de Vencimento');
        $this->assertEquals(200, $linhaDigitavel['valor'], 'Valor');
        $this->assertEquals('29620870000000017-X', $linhaDigitavel['nosso_numero'], 'Nosso número');

        $linhaDigitavel = LinhaDigitavel::ler('00190000090296208700900001634179171670000004000');
        $this->assertEquals('001', $linhaDigitavel['codigo_banco'], 'Código do banco');
        $this->assertEquals('9', $linhaDigitavel['moeda'], 'Moeda');
        $this->assertEquals('7167', $linhaDigitavel['fator_vencimento'], 'Fator de Vencimento');
        $this->assertEquals(40, $linhaDigitavel['valor'], 'Valor');
        $this->assertEquals('29620870000001634-3', $linhaDigitavel['nosso_numero'], 'Nosso número');

        $linhaDigitavel = LinhaDigitavel::ler('00190000090299480600400004137170673560000008990');
        $this->assertEquals('001', $linhaDigitavel['codigo_banco'], 'Código do banco');
        $this->assertEquals('9', $linhaDigitavel['moeda'], 'Moeda');
        $this->assertEquals('7356', $linhaDigitavel['fator_vencimento'], 'Fator de Vencimento');
        $this->assertEquals(89.9, $linhaDigitavel['valor'], 'Valor');
        $this->assertEquals('29948060000004137-4', $linhaDigitavel['nosso_numero'], 'Nosso número');
    }

    public function testLerLinhaDigitavelSantander()
    {
        $linhaDigitavel = LinhaDigitavel::ler('03399897093350000000601413301019174080000000552');
        $this->assertEquals('033', $linhaDigitavel['codigo_banco'], 'Código do banco');
        $this->assertEquals('9', $linhaDigitavel['moeda'], 'Moeda');
        $this->assertEquals('7408', $linhaDigitavel['fator_vencimento'], 'Fator de Vencimento');
        $this->assertEquals(5.52, $linhaDigitavel['valor'], 'Valor');

        $linhaDigitavel = LinhaDigitavel::ler('03399897093350000000600193701018874000000006000');
        $this->assertEquals('033', $linhaDigitavel['codigo_banco'], 'Código do banco');
        $this->assertEquals('9', $linhaDigitavel['moeda'], 'Moeda');
        $this->assertEquals('7400', $linhaDigitavel['fator_vencimento'], 'Fator de Vencimento');
        $this->assertEquals(60, $linhaDigitavel['valor'], 'Valor');
        $this->assertEquals('000000000193-7', $linhaDigitavel['nosso_numero'], 'Nosso número');

        $linhaDigitavel = LinhaDigitavel::ler('03399897093351234567089012301019474000001234567');
        $this->assertEquals('033', $linhaDigitavel['codigo_banco'], 'Código do banco');
        $this->assertEquals('9', $linhaDigitavel['moeda'], 'Moeda');
        $this->assertEquals('7400', $linhaDigitavel['fator_vencimento'], 'Fator de Vencimento');
        $this->assertEquals(12345.67, $linhaDigitavel['valor'], 'Valor');
        $this->assertEquals('123456789012-3', $linhaDigitavel['nosso_numero'], 'Nosso número');
    }
}
