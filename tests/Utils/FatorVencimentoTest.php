<?php

namespace Cobranca\Tests\Boleto;

use Cobranca\Utils\FatorVencimento;

final class FatorVencimentoTest extends \Cobranca\Tests\AbstractTestCase
{
    public function testFatorVencimento()
    {
      $arr = array(
        1000 => new \DateTime('2000-07-03'),
        1001 => new \DateTime('2000-07-04'),
        1002 => new \DateTime('2000-07-05'),
        1667 => new \DateTime('2002-05-01'),
        2046 => new \DateTime('2003-05-15'),
        3737 => new \DateTime('2007-12-31'),
        4789 => new \DateTime('2010-11-17'),
        7400 => new \DateTime('2018-01-10'),
        9999 => new \DateTime('2025-02-21'),
      );
      foreach ($arr as $key => $value) {
        $this->assertEquals($key, FatorVencimento::calcular($value));
      }
    }
}
