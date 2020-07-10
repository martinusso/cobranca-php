<?php

namespace Cobranca\Tests\Boleto;

use Cobranca\Utils\Modulo10;

final class Modulo10Test extends \Cobranca\Tests\AbstractTestCase
{
    public function testCalculoModulo10()
    {
        $this->assertEquals(5, Modulo10::calcular('001905009'));
        $this->assertEquals(9, Modulo10::calcular('4014481606'));
        $this->assertEquals(4, Modulo10::calcular('0680935031'));
    }
}
