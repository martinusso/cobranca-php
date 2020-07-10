<?php

namespace Cobranca\Utils;

use Cobranca\Utils\Modulo11;

/**
 * Código de barras
 *
 * public static function create($banco, $fator_vencimento, $valor, $campo_livre, $codigo_moeda = 9)
 *
 * a) O código de barras é composto por dois campos:
 * b) campo obrigatório: determinado pela FEBRABAN e comum a todos dos bancos;
 * c) campo livre: determinado por cada banco de acordo com a modalidade de Cobrança utilizada pelo cliente;
 * d) Deve conter 44 posições, disposto da seguinte forma:
 *
 * Posição  Tamanho Picture   Conteúdo
 * 01 a 03  03      9(03)     Código do Banco na Câmara de Compensação = '001'
 * 04 a 04  01      9(01)     Código da Moeda = 9 (Real)
 * 05 a 05  01      9(01)     Digito Verificador (DV) do código de Barras*
 * 06 a 09  04      9(04)     Fator de Vencimento **
 * 10 a 19  10      9(08)V(2) Valor
 * 20 a 44  03      9(03)     Campo Livre ***
 * * Para cálculo do DV do Código Barras, consulte Anexo VI
 * ** Para cálculo do Fator de Vencimento, consulte o Anexo IV
 * *** Os padrões do BB estão identificados nos Anexos VII, VIII, IX e X
 */
class CodigoDeBarras
{
    public static function create($banco, $codigo_moeda, $fator_vencimento, $valor, $campo_livre)
    {
        $banco = str_pad($banco, 3, '0', STR_PAD_LEFT);
        $valor = round($valor, 2) * 100;
        $valor = str_pad($valor, 10, '0', STR_PAD_LEFT);

        $s = $banco . $codigo_moeda . $fator_vencimento . $valor . $campo_livre;
        $dv = Modulo11::calcular($s, [0 => 1]);

        return $banco . $codigo_moeda . $dv . $fator_vencimento . $valor . $campo_livre;
    }
}
