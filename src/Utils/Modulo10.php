<?php

namespace Cobranca\Utils;

class Modulo10
{
  public static function calcular($str)
  {
    $soma = 0;
    $multiplicador = 2;
    $arr = str_split($str);
    $arr = array_reverse($arr);
    foreach ($arr as $value) {
      $calc = $value * $multiplicador;
      $soma += static::somaDigitos($calc);
      $multiplicador = ($multiplicador == 2) ? 1 : 2;
    }
    $valor = (10 - ($soma % 10));
    return ($valor == 10) ? 0 : $valor;
  }

  public static function somaDigitos($digitos) {
      $soma = 0;
      $arr = str_split($digitos);
      foreach ($arr as $value) {
        $soma += $value;
      }
      return $soma;
  }
}
