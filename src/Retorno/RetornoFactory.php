<?php

namespace Cobranca\Retorno;

use Cobranca\Banco;
use Cobranca\Retorno\CNAB400\BancoBrasil;
use Cobranca\Retorno\CNAB400\Santander;

/**
* Fábrica de arquivos de retorno
*/
class RetornoFactory
{
    /**
    * Cria instância
    *
    * @param string $banco
    * @param string $layout = null
    */
    public static function create($banco, $layout = null)
    {
        switch ($banco) {
            case Banco::BancoDoBrasil:
                return new BancoBrasil();
            case Banco::Santander:
                return new Santander();
            default:
                throw new \Exception("Banco {$banco} não suportado", 1);
        }
    }

    /**
    * Cria instância por arquivo
    *
    * @param string $banco
    * @param string $layout = null
    */
    public static function porArquivo($arquivo)
    {
        $arquivo = trim($arquivo);
        $registros = explode("\n", trim($arquivo));
        if (count($registros) < 3) {
            throw new \Exception("Arquivo de retorno não suportado", 1);
        }

        if (self::isBancoBrasil($arquivo)) {
            return self::create('001', 'CNAB400');
        }

        if (self::isSantander($arquivo)) {
            return self::create('033', 'CNAB400');
        }
        throw new \Exception("Arquivo de retorno não suportado", 1);
    }

    private static function isBancoBrasil($arquivo)
    {
        if (substr($arquivo, 2, 7) != 'RETORNO') {
            return false;
        }

        if (substr($arquivo, 11, 8) != 'COBRANCA') {
            return false;
        }

        if (substr($arquivo, 11, 8) != 'COBRANCA') {
            return false;
        }

        if (substr($arquivo, 76, 3) != '001') {
            return false;
        }
        return true;
    }

    private static function isSantander($arquivo)
    {
        if (substr($arquivo, 2, 7) !== 'RETORNO') {
            return false;
        }

        if (substr($arquivo, 11, 8) !== 'COBRANCA') {
            return false;
        }

        if (substr($arquivo, 11, 8) !== 'COBRANCA') {
            return false;
        }

        if (substr($arquivo, 76, 3) !== '033') {
            return false;
        }
        return true;
    }
}
