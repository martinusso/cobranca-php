<?php

namespace Cobranca\Utils;

use Cobranca\Utils\Modulo10;

/**
*  Linha Digitável – Representação Numérica do Código de Barras
*  public static function create($codigo_barras)
*/
class LinhaDigitavel
{
    protected static $codigo_barras;

    public static function create($codigo_barras)
    {
        static::$codigo_barras = $codigo_barras;
        return static::campo1() . static::campo2() . static::campo3() . static::campo4() . static::campo5();
    }

    public static function ler($linhaDigitavel)
    {
        $codigoBanco = substr($linhaDigitavel, 0, 3);
        $nossoNumero = null;
        switch ($codigoBanco) {
          case '001':
              $nossoNumero = substr($linhaDigitavel, 11, 9) . substr($linhaDigitavel, 21, 8);
              $nossoNumero .= '-' . Modulo11::calcular($nossoNumero, array(10 => 'X'));
              break;
          case '033':
              $nossoNumero = substr($linhaDigitavel, 13, 7) . substr($linhaDigitavel, 21, 5) . '-' . substr($linhaDigitavel, 26, 1);
              break;
      }
        $valor = substr($linhaDigitavel, 37, 10);
        $valor = intval($valor)/100;
        return [
          'codigo_banco' => $codigoBanco,
          'moeda' => substr($linhaDigitavel, 3, 1),
          'fator_vencimento' => substr($linhaDigitavel, 33, 4),
          'valor' => $valor,
          'nosso_numero' => $nossoNumero,
      ];
    }

    /**
       * Campo 1 da linha digitável
       *
     * a) Campo 1: AAABC.CCCCX
     * A = Número Código da IF Destinatária no SILOC
     * B = Código da moeda (9) -Real
     * C = Posições 20 a 24 do código de barras
     * X = DV do Campo 1 (calculado de acordo com o Módulo 10 – anexo V)
       */
    private static function campo1()
    {
        $cb = static::$codigo_barras;
        $campo1 = substr($cb, 0, 3) .
      substr($cb, 3, 1) .
      substr($cb, 19, 5);
        return $campo1 . Modulo10::calcular($campo1);
    }

    /**
       * Campo 2 da linha digitável
       *
     * b) Campo 2: DDDDD.DDDDDY
     * D = Posições 25 a 34 do código de barras
     * Y = DV do Campo 2 (calculado de acordo com o Módulo 10 – anexo V)
       */
    private static function campo2()
    {
        $campo2 = substr(static::$codigo_barras, 24, 10);
        return $campo2 . Modulo10::calcular($campo2);
    }

    /**
       * Campo 3 da linha digitável
       *
     * c) Campo 3: EEEEE.EEEEEZ
     * E = Posições 35 a 44 do código de barras
     * Z = DV do Campo 3 (calculado de acordo com o Módulo 10 – anexo V)
       */
    private static function campo3()
    {
        $campo3 = substr(static::$codigo_barras, 34, 10);
        return $campo3 . Modulo10::calcular($campo3);
    }

    /**
       * Campo 4 da linha digitável
       *
     * d) Campo 4: K
     * K = DV do código de barras (calculado de acordo com o Módulo 11 – anexo VI)
       */
    private static function campo4()
    {
        return substr(static::$codigo_barras, 4, 1);
    }

    /**
       * Campo 5 da linha digitável
       *
     * e) Campo 5: UUUUVVVVVVVVVV
     * U = Fator de Vencimento (cálculo conforme anexo IV)
     * V = Valor do boleto de pagamento (com duas casas decimais, sem ponto e vírgula. Em caso de moeda variável, informar zeros)
       */
    private static function campo5()
    {
        $cb = static::$codigo_barras;
        return substr($cb, 5, 4) . substr($cb, 9, 10);
    }
}
