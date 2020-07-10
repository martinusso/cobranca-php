<?php

namespace Cobranca\Tests\Boleto;

use Cobranca\Utils\CodigoDeBarras;

final class CodigoDeBarrasTest extends \Cobranca\Tests\AbstractTestCase
{
    public function testCodigoDeBarras()
    {
        $codigo_barras = CodigoDeBarras::create('001', '9', '0000', 5.45, '0000002962087000000000117');
        $this->assertEquals('00197000000000005450000002962087000000000117', $codigo_barras, 'boleto número 1');

        $codigo_barras = CodigoDeBarras::create('001', '9', '0000', 42.09, '0000002962087000000000217');
        $this->assertEquals('00199000000000042090000002962087000000000217', $codigo_barras, 'boleto número 2');

        $codigo_barras = CodigoDeBarras::create('001', '9', '0000', 176.72, '0000002962087000000000317');
        $this->assertEquals('00198000000000176720000002962087000000000317', $codigo_barras, 'boleto número 3');

        $codigo_barras = CodigoDeBarras::create('001', '9', '0000', 1540.36, '0000002962087000000000417');
        $this->assertEquals('00199000000001540360000002962087000000000417', $codigo_barras, 'boleto número 4');

        $codigo_barras = CodigoDeBarras::create('033', '9', '7400', 12345.67, '9897033512345678901230101');
        $this->assertEquals('03394740000012345679897033512345678901230101', $codigo_barras, 'boleto santander');
    }
}
