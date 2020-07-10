<?php

namespace Cobranca\Tests\Retorno;

use Cobranca\Retorno\RetornoFactory;

final class RetornoFactoryTest extends \Cobranca\Tests\AbstractTestCase
{
    /**
    * @expectedException Exception
    * @expectedExceptionMessage Banco 000 não suportado
    */
    public function testProcessarRetornoBancoNaoSuportado()
    {
        RetornoFactory::create('000');
    }

    public function testProcessarRetornoBancoSuportado()
    {
        RetornoFactory::create('001');
        $this->assertTrue(true, 'Nao deve gerar exceção');
    }

    /**
    * @expectedException Exception
    * @expectedExceptionMessage Arquivo de retorno não suportado
    */
    public function testDeveGerarExcecaoQuandoBancoNaoSuportado()
    {
        $content = '0123456798';
        RetornoFactory::porArquivo($content);
    }

    /**
    * @expectedException Exception
    * @expectedExceptionMessage Arquivo de retorno não suportado
    */
    public function testDeveGerarExcecaoQuandoPassarArquivoComMenos3Linhas()
    {
        $content = "02RETORNO01COBRANCA       36803000153990000000ACBDEFGH PAYMENT SECURITY LTDA001BANCO DO BRASIL
            SEGUNDA LINHA";

        RetornoFactory::porArquivo($content);
    }

    /**
    * @expectedException Exception
    * @expectedExceptionMessage Arquivo de retorno não suportado
    */
    public function testDeveGerarExcecaoQuandoNaoPossuiPalavraRetorno()
    {
        $content = "02REMESSA01COBRANCA       36803000153990000000ACBDEFGH PAYMENT SECURITY LTDA001BANCO DO BRASIL
            SEGUNDA LINHA
            TERCEIRA LINHA";

        RetornoFactory::porArquivo($content);
    }

    /**
    * @expectedException Exception
    * @expectedExceptionMessage Arquivo de retorno não suportado
    */
    public function testDeveGerarExcecaoQuandoNaoPossuiPalavraCobranca()
    {
        $content = "02RETORNO01BLOQUETO       36803000153990000000ACBDEFGH PAYMENT SECURITY LTDA001BANCO DO BRASIL
            SEGUNDA LINHA
            TERCEIRA LINHA";

        RetornoFactory::porArquivo($content);
    }

    /**
    * @expectedException Exception
    * @expectedExceptionMessage Arquivo de retorno não suportado
    */
    public function testDeveGerarExcecaoQuandoNaoPossuiCodigoBancoBrasil()
    {
        $content = "02RETORNO01COBRANCA       36803000153990000000ACBDEFGH PAYMENT SECURITY LTDA999BANCO DO BRASIL
            SEGUNDA LINHA
            TERCEIRA LINHA";

        RetornoFactory::porArquivo($content);
    }

    public function testFabricaRetornoPorArquivoBancoBrasilCBR643()
    {
        $content = file_get_contents('tests/resources/CBR643562303201713947.ret');
        $retornBancoBrasil = RetornoFactory::porArquivo($content);
        $this->assertEquals('001', $retornBancoBrasil->banco());
    }
}
