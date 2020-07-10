<?php

namespace Cobranca\Utils;

class Functions
{
    public static function zeros($value, $quantidade, $force = true, $ultimos = true)
    {
        if ($force && (strlen($value) > $quantidade)) {
            if ($ultimos) {
                $value = substr($value, strlen($value)-$quantidade, $quantidade);
            } else {
                $value = substr($value, 0, $quantidade);
            }
        }
        return str_pad($value, $quantidade, '0', STR_PAD_LEFT);
    }

    public static function brancos($value, $quantidade, $force = true)
    {
        $novaString = self::sanitize($value);
        if ($force) {
            $novaString = substr($novaString, 0, $quantidade);
        }

        return str_pad($novaString, $quantidade, ' ', STR_PAD_RIGHT);
    }

    public static function sanitize($s)
    {
        $novaString = preg_replace(
            array("/(á|à|ã|â|ä|Á|À|Ã|Â|Ä)/","/(é|è|ê|ë|É|È|Ê|Ë)/","/(í|ì|î|ï|Í|Ì|Î|Ï)/","/(ó|ò|õ|ô|ö|Ó|Ò|Õ|Ô|Ö)/","/(ú|ù|û|ü|Ú|Ù|Û|Ü)/","/(ñ|Ñ)/","/(ç|Ç)/"),
            explode(" ","A E I O U N C"),$s);
        return strtoupper(utf8_decode($novaString));
    }

    public static function onlyNumbers($value)
    {
        return preg_replace("/[^0-9]/", "", $value);
    }
}
