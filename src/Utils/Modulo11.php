<?php

namespace Cobranca\Utils;

class Modulo11
{
  public static function calcular($str, $mapeamento = null)
  {
    $i = 0;
    $multiplicadores = [2, 3, 4, 5, 6, 7, 8, 9];
    $sum = 0;
    $digitos = str_split($str);
    $digitos = array_reverse($digitos);
    foreach ($digitos as $value) {
      $sum += ($value * $multiplicadores[$i]);
      $i = ($i == 7) ? 0 : $i+1;
    }
    $mod = ($sum % 11);
    $valor = ($mod != 0) ? (11 - $mod) : 0;

    if ($mapeamento && isset($mapeamento[$valor])) {
        return $mapeamento[$valor];
    } else {
      $mapeamento_padrao = array(10 => 1, 11 => 1);
      if (isset($mapeamento_padrao[$valor])) {
  	     return $mapeamento_padrao[$valor];
      }
      return $valor;
    }
  }
}
