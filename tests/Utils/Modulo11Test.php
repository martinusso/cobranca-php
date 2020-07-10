<?php

namespace Cobranca\Tests\Boleto;

use Cobranca\Utils\Modulo11;

final class Modulo11Test extends \Cobranca\Tests\AbstractTestCase
{
    public function testCalculoModulo11()
    {
        $this->assertEquals(3, Modulo11::calcular('0019373700000001000500940144816060680935031'));
    }

    public function testCalculoModulo11ComMapeamento()
    {
        $this->assertEquals('X', Modulo11::calcular('4556', [10 => 'X']));
        $this->assertEquals('3', Modulo11::calcular('3680', array(10 => 'X')));
        $this->assertEquals('9', Modulo11::calcular('85068014982', array(10 => 'X')));
        $this->assertEquals('1', Modulo11::calcular('05009401448', array(10 => 'X')));
        $this->assertEquals('2', Modulo11::calcular('12387987777700168', array(10 => 'X')));
        $this->assertEquals('8', Modulo11::calcular('4042', array(10 => 'X')));
        $this->assertEquals('0', Modulo11::calcular('61900', array(10 => 'X')));
        $this->assertEquals('6', Modulo11::calcular('0719', array(10 => 'X')));
        $this->assertEquals('0', Modulo11::calcular('15399', array(10 => 'X')));
        $this->assertEquals('3', Modulo11::calcular('123456789012'));
    }
}
