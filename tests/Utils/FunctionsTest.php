<?php

namespace Cobranca\Tests\Boleto;

use Cobranca\Utils\Functions;

final class FunctionsTest extends \Cobranca\Tests\AbstractTestCase
{
    public function testZeros()
    {
        $this->assertEquals('0', Functions::zeros('0', 1));
        $this->assertEquals('0000000', Functions::zeros('0', 7));
        $this->assertEquals('000000000000000000000000000000000000000000', Functions::zeros('0', 42));

        $this->assertEquals('00061900', Functions::zeros('61900', 8), '0');
        $this->assertEquals('', Functions::zeros('1', 0), '0');
        $this->assertEquals('1', Functions::zeros('1', 0, false), '0');
        $this->assertEquals('2', Functions::zeros('2', 1), '1');
        $this->assertEquals('890', Functions::zeros('1234567890', 3), '7');
        $this->assertEquals('1234567890', Functions::zeros('1234567890', 3, false), '7');
        $this->assertEquals('0000003', Functions::zeros('3', 7), '7');
        $this->assertEquals('000000000000000000000000000000000000000004', Functions::zeros('4', 42), '42');
    }

    public function testBrancos()
    {
        $this->assertEquals('', Functions::brancos('', 0), '0 espaço em branco');
        $this->assertEquals(' ', Functions::brancos('', 1), '1 espaço em branco');
        $this->assertEquals('       ', Functions::brancos('', 7), '7 espaços em branco');
        $this->assertEquals('                                          ', Functions::brancos('', 42), '42 espaços em branco');
        $this->assertEquals('SETE   ', Functions::brancos('Sete', 7));
        $this->assertEquals('A VIDA, O UNIVERSO E TUDO MAIS            ', Functions::brancos('A Vida, o Universo e Tudo Mais', 42));

        $this->assertEquals('QUARENTA E', Functions::brancos('Quarenta e dois', 10));
        $this->assertEquals('QUARENTA E DOIS', Functions::brancos('Quarenta e dois', 10, false));

        $vitoria = Functions::brancos('Vitória', 15);
        $this->assertEquals('VITORIA        ', $vitoria, 'Vitória');
    }

    public function testSanitize()
    {
        $this->assertEquals('ENDERECO', Functions::sanitize('ENDEREÇO'), 'Endereço');
        $this->assertEquals('ATENCAO', Functions::sanitize('Atenção'), 'Atenção');
        $this->assertEquals('LIQUIDACAO', Functions::sanitize('Liquidação'), 'Liquidação');
        $this->assertEquals('TRANSFERENCIA', Functions::sanitize('Transferência'), 'Transferência');
        $this->assertEquals('BANCARIA', Functions::sanitize('Bancária'), 'Bancária');
        $this->assertEquals('VITORIA', Functions::sanitize('Vitória'), 'Vitória');
        $this->assertEquals('ULTIMOS', Functions::sanitize('Últimos'), 'Últimos');
    }
}
