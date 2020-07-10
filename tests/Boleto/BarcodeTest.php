<?php

namespace Cobranca\Tests\Boleto;

use Cobranca\Boleto\Barcode;

final class BarcodeTest extends \Cobranca\Tests\AbstractTestCase
{
  public function testGerarBarcode()
  {
    $img = Barcode::generate('00191376900000135001238012345640420006190018');
    $this->assertContains('data:image/png;base64,', $img);
  }

  /**
   * @expectedException InvalidArgumentException
   * @expectedExceptionMessage Código de barras inválido
   */
  public function testDeveGerarExcecaoQuandoPassaValorEmBranco()
  {
    Barcode::generate('');
  }

  /**
   * @expectedException InvalidArgumentException
   * @expectedExceptionMessage Código de barras inválido
   */
  public function testDeveGerarExcecaoQuandoPassaValorNulo()
  {
    Barcode::generate(null);
  }
}
