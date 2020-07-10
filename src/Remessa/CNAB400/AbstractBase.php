<?php

namespace Cobranca\Remessa\CNAB400;

use Cobranca\Remessa\Remessa;
use Cobranca\Utils\Functions;

/**
 * Classe base para Arquivo de Remessa CNAB400
 */
abstract class AbstractBase extends \Cobranca\Remessa\AbstractRemessa
{
    /**
    * Retorna as linhas do arquivo de remessa
    *
    * @param $remessa
    * @return string Retorna as linhas do arquivo de remessa
    */
    public function strings()
    {
        $arr[] = $this->getHeader();
        foreach ($this->getPagamentos() as $key => $value) {
            $arr[] = Functions::sanitize($value);
        }
        $arr[] = $this->getTrailler();
        return implode("\n", $arr);
    }
}
