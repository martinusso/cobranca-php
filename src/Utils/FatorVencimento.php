<?php

namespace Cobranca\Utils;

class FatorVencimento
{
  public static function calcular($data)
  {
    $data_base = new \DateTime('1997-10-07');
    $intervalo = $data->diff($data_base);
     return strval($intervalo->format('%a'));
  }
}
