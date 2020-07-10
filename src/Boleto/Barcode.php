<?php

namespace Cobranca\Boleto;

class Barcode
{
    /**
    * Generate Barcode
    *
    * @param $value Código de barras em dígitos
    *
    * @throws InvalidArgumentException Gera exceção quando passa o valor do código de barras inválido
    */
    static public function generate($value, $img_width = 500, $img_height = 70)
    {
        if (!$value or $value == '') {
            throw new \InvalidArgumentException("Código de barras inválido", 1);
        }

        $width_bar = 415;
        $height_bar = 58;
        $thin = 1;
        $wide = static::getWide();
        $top = 0;
        $barcodes = static::barcodes();

        $new_img = null;
        $img = imagecreatetruecolor($width_bar, $height_bar) or die('Cannot initialize new image stream');
        try {
            $cl_black = imagecolorallocate($img, 0, 0, 0);
            $cl_white = imagecolorallocate($img, 255, 255, 255);

            imagefilledrectangle($img, 0, 0, $width_bar, $height_bar, $cl_white);
            imagefilledrectangle($img, 5, $top, 5, $height_bar, $cl_black);
            imagefilledrectangle($img, 6, $top, 6, $height_bar, $cl_white);
            imagefilledrectangle($img, 7, $top, 7, $height_bar, $cl_black);
            imagefilledrectangle($img, 8, $top, 8, $height_bar, $cl_white);

            $pos = 9;
            $text = $value;
            if ((strlen($text) % 2) <> 0) {
                $text = '0' . $text;
            }

            while (strlen($text) > 0) {
                $i = round(static::JSK_left($text, 2));
                $text = static::JSK_right($text, strlen($text)-2);
                $f = $barcodes[$i];

                for ($i = 1; $i < 11; $i += 2) {
                    if (substr($f, ($i-1), 1) == '0') {
                        $f1 = $thin;
                    } else {
                        $f1 = $wide;
                    }
                    imagefilledrectangle($img, $pos, $top, $pos - 1 + $f1, $height_bar, $cl_black);
                    $pos = $pos + $f1 ;

                    if (substr($f, $i, 1) == '0') {
                        $f2 = $thin;
                    }else{
                        $f2 = $wide;
                    }
                    imagefilledrectangle($img, $pos, $top, $pos-1+$f2, $height_bar, $cl_white);
                    $pos = $pos + $f2 ;
                }
            }
            imagefilledrectangle($img, $pos, $top, $pos-1+$wide, $height_bar, $cl_black);
            $pos = $pos + $wide;

            imagefilledrectangle($img, $pos, $top, $pos-1+$thin, $height_bar, $cl_white);
            $pos = $pos + $thin;

            imagefilledrectangle($img, $pos, $top, $pos-1+$thin, $height_bar, $cl_black);
            $pos = $pos + $thin;

            if (($img_width != $width_bar) || ($img_height != $height_bar)) {
                $img = imagescale($img, $img_width, $img_height);
            }
            ob_start();
            imagepng($img);
            $buffer = ob_get_clean();
            if (ob_get_contents()) ob_end_clean();
            return 'data:image/png;base64,' . base64_encode($buffer);
        } finally {
            imagedestroy($img);
        }
    }

    static private function barcodes()
    {
        $barcodes = array(
            0 => '00110',
            1 => '10001',
            2 => '01001',
            3 => '11000',
            4 => '00101',
            5 => '10100',
            6 => '01100',
            7 => '00011',
            8 => '10010',
            9 => '01010');

        for ($f1 = 9; $f1 >= 0; $f1--) {
            for ($f2 = 9; $f2 >= 0; $f2--) {
                $f = ($f1 * 10) + $f2;
                $texto = '';
                for ($i = 1; $i < 6; $i++) {
                    $texto .= substr($barcodes[$f1], ($i - 1), 1) . substr($barcodes[$f2], ($i - 1), 1);
                }
                $barcodes[$f] = $texto;
            }
        }
        return $barcodes;
    }

    static private function getWide()
    {
        $win32 = (array_key_exists('REMOTE_HOST', $_SERVER)) ? substr_count(strtoupper($_SERVER["SERVER_SOFTWARE"]), 'WIN32') : false;
        return ($win32) ? 2.72 : 3;
    }

    static private function JSK_left($input, $comp)
    {
        return substr($input, 0, $comp);
    }

    static private function JSK_right($input, $comp)
    {
        return substr($input, strlen($input) - $comp, $comp);
    }
}
