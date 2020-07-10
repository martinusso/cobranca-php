<?php

namespace Cobranca\Tests\Retorno\CNAB400;

use Cobranca\Retorno\CNAB400\Santander;

final class SantanderTest extends \Cobranca\Tests\AbstractTestCase
{
    public function testProcessarRetornoCNAB400()
    {
        $conteudoArquivo = file_get_contents('tests/resources/Santander.RET');
        $santander = new Santander();
        $santander->processarRetorno($conteudoArquivo);
        $registros = $santander->registrosDetalhe();
        $this->assertEquals('5225', $santander->agencia(), 'Agência');
        $this->assertNull($santander->agenciaDV(), 'DV da agência');
        $this->assertEquals('21194667', $santander->contaCorrente(), 'Conta corrente');
        $this->assertNull($santander->contaCorrenteDV(), 'DV da conta corrente');
        $this->assertEquals('ACBDEFGH PAYMENT SECURITY LTDA', $santander->nomeCedente(), 'Nome do cedente');
        $this->assertEquals('9889426', $santander->convenio(), 'Convênio');
    }
}
