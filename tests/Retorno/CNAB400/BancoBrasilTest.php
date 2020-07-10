<?php

namespace Cobranca\Tests\Retorno\CNAB400;

use Cobranca\Retorno\CNAB400\BancoBrasil;

final class BancoBrasilTest extends \Cobranca\Tests\AbstractTestCase
{
    public function testProcessarRetornoCNAB400()
    {
        $conteudoArquivo = file_get_contents('tests/resources/CNAB400_BancoBrasil.RET');
        $bb = new BancoBrasil();
        $bb->processarRetorno($conteudoArquivo);
        $this->assertEquals('3603', $bb->agencia(), 'Agência');
        $this->assertEquals('X', $bb->agenciaDV(), 'DV da agência');
        $this->assertEquals('00038529', $bb->contaCorrente(), 'Conta corrente');
        $this->assertEquals('8', $bb->contaCorrenteDV(), 'DV da conta corrente');
        $this->assertEquals('ASSOCIACAO BRASILEIRA DE ANTRO', $bb->nomeCedente(), 'Nome do cedente');
        $this->assertEquals('2019029', $bb->convenio(), 'Convênio');
    }

    public function testProcessarRetornoCBR643()
    {
        $conteudoArquivo = file_get_contents('tests/resources/CBR643_BancoBrasil.RET');
        $bb = new BancoBrasil();
        $bb->processarRetorno($conteudoArquivo);
        $this->assertEquals('3325', $bb->agencia(), 'Agência');
        $this->assertEquals('1', $bb->agenciaDV(), 'DV da agência');
        $this->assertEquals('00028935', $bb->contaCorrente(), 'Conta corrente');
        $this->assertEquals('3', $bb->contaCorrenteDV(), 'DV da conta corrente');
        $this->assertEquals('INSTITUTO MATO-GROSSENSE DO AL', $bb->nomeCedente(), 'Nome do cedente');
        $this->assertEquals('1622420', $bb->convenio(), 'Convênio');

        // Registros detalhe
        $registros_detalhe = $bb->registrosDetalhe();
        $this->assertEquals(26, count($registros_detalhe), 'Registros detalhe');

        $reg = $registros_detalhe[0];
        $this->assertEquals('3325', $reg['agencia'], 'Agência');
        $this->assertEquals('1', $reg['agencia_dv'], 'DV da agência');
        $this->assertEquals('00028935', $reg['conta'], 'Conta corrente');
        $this->assertEquals('3', $reg['conta_dv'], 'DV da conta corrente');
        $this->assertEquals('1622420', $reg['convenio'], 'Convênio');
        $this->assertEquals('16224200000000003', $reg['nosso_numero'], 'Nosso número');
        $this->assertEquals('1', $reg['tipo_cobranca'], 'Tipo cobrança');
        $this->assertEquals('01', $reg['natureza_recebimento'], 'Natureza do recebimento');
        $this->assertEquals('019', $reg['variacao_carteira'], 'Variação carteira');
        $this->assertEquals('0000', $reg['taxa_desconto'], 'Desconto');
        $this->assertEquals('0000', $reg['taxa_iof'], 'IOF');
        $this->assertEquals('18', $reg['carteira'], 'Carteira');
        $this->assertEquals('2009-01-22', $reg['data_credito'], 'Data crédito');
        $this->assertEquals('', $reg['data_vencimento'], 'Data vencimento');
        $this->assertEquals(90.64, $reg['valor_titulo'], 'Valor do título');
        $this->assertEquals('001', $reg['banco_recebedor'], 'Código do banco recebedor');
        $this->assertEquals('1492', $reg['agencia_recebedor'], 'Prefixo da agência recebedora');
        $this->assertEquals('3', $reg['agencia_dv_recebedor'], 'DV prefixo recebedora');
        $this->assertEquals('00', $reg['especie_titulo'], 'Espécie do título');
        $this->assertEquals('2009-01-22', $reg['data_credito'], 'Data crédito');
        $this->assertEquals('2009-01-20', $reg['data_liquidacao'], 'Data do pagamento');
        $this->assertEquals(5.00, $reg['valor_tarifa'], 'Valor da tarifa');
        $this->assertEquals(0, $reg['outras_despesas'], 'Outras despesas');
        $this->assertEquals(0, $reg['juros_desconto'], 'Juros do desconto');
        $this->assertEquals(0, $reg['iof_desconto'], 'IOF do desconto');
        $this->assertEquals(0, $reg['valor_abatimento'], 'Valor do abatimento');
        $this->assertEquals(0, $reg['desconto'], 'Desconto concedido  (diferença entre valor do título e valor recebido)');
        $this->assertEquals(90.64, $reg['valor_pagto'], 'Valor recebido (valor recebido parcial)');
        $this->assertEquals(0, $reg['juros_mora'], 'Juros de mora');
        $this->assertEquals(0, $reg['outros_recebimentos'], 'Outros recebimentos');
        $this->assertEquals(0, $reg['abatimento_nao_aproveitado'], 'Abatimento não aproveitado pelo sacado');
        $this->assertEquals(85.64, $reg['valor_lancamento'], 'Valor do lançamento');
        $this->assertEquals('crédito', $reg['indicativo_lancamento'], 'Indicativo de débito/crédito');
        $this->assertEquals('0', $reg['indicativo_valor'], 'Indicador de valor');
        $this->assertEquals(0, $reg['valor_ajuste'], 'Valor do ajuste');
        $this->assertEquals('000002', $reg['sequencial_registro'], 'Sequencial do registro');

        $reg = $registros_detalhe[7];
        $this->assertEquals('3325', $reg['agencia'], 'Agência, index 7');
        $this->assertEquals('1', $reg['agencia_dv'], 'DV da agência, index 7');
        $this->assertEquals('00028935', $reg['conta'], 'Conta corrente, index 7');
        $this->assertEquals('3', $reg['conta_dv'], 'DV da conta corrente, index 7');
        $this->assertEquals('1622420', $reg['convenio'], 'Convênio, index 7');
        $this->assertEquals('16224200000000036', $reg['nosso_numero'], 'Nosso número, index 7');
        $this->assertEquals('1', $reg['tipo_cobranca'], 'Tipo cobrança, index 7');
        $this->assertEquals('01', $reg['natureza_recebimento'], 'Natureza do recebimento, index 7');
        $this->assertEquals('019', $reg['variacao_carteira'], 'Variação carteira, index 7');
        $this->assertEquals('0000', $reg['taxa_desconto'], 'Desconto, index 7');
        $this->assertEquals('0000', $reg['taxa_iof'], 'IOF, index 7');
        $this->assertEquals('18', $reg['carteira'], 'Carteira, index 7');
        $this->assertEquals('2009-01-22', $reg['data_credito'], 'Data crédito, index 7');
        $this->assertEquals('', $reg['data_vencimento'], 'Data vencimento, index 7');
        $this->assertEquals(561.00, $reg['valor_titulo'], 'Valor do título, index 7');
        $this->assertEquals('748', $reg['banco_recebedor'], 'Código do banco recebedor, index 7');
        $this->assertEquals('0811', $reg['agencia_recebedor'], 'Prefixo da agência recebedora, index 7');
        $this->assertEquals('0', $reg['agencia_dv_recebedor'], 'DV prefixo recebedora, index 7');
        $this->assertEquals('00', $reg['especie_titulo'], 'Espécie do título, index 7');
        $this->assertEquals('2009-01-22', $reg['data_credito'], 'Data crédito, index 7');
        $this->assertEquals('2009-01-20', $reg['data_liquidacao'], 'Data de pagamento (DDMMAA), index 7');
        $this->assertEquals(5.00, $reg['valor_tarifa'], 'Valor da tarifa, index 7');
        $this->assertEquals(0, $reg['outras_despesas'], 'Outras despesas, index 7');
        $this->assertEquals(0, $reg['juros_desconto'], 'Juros do desconto, index 7');
        $this->assertEquals(0, $reg['iof_desconto'], 'IOF do desconto, index 7');
        $this->assertEquals(0, $reg['valor_abatimento'], 'Valor do abatimento, index 7');
        $this->assertEquals(0, $reg['desconto'], 'Desconto concedido  (diferença entre valor do título e valor recebido), index 7');
        $this->assertEquals(561.00, $reg['valor_pagto'], 'Valor recebido (valor recebido parcial), index 7');
        $this->assertEquals(0, $reg['juros_mora'], 'Juros de mora, index 7');
        $this->assertEquals(0, $reg['outros_recebimentos'], 'Outros recebimentos, index 7');
        $this->assertEquals(0, $reg['abatimento_nao_aproveitado'], 'Abatimento não aproveitado pelo sacado, index 7');
        $this->assertEquals(556.00, $reg['valor_lancamento'], 'Valor do lançamento, index 7');
        $this->assertEquals('crédito', $reg['indicativo_lancamento'], 'Indicativo de débito/crédito, index 7');
        $this->assertEquals('0', $reg['indicativo_valor'], 'Indicador de valor, index 7');
        $this->assertEquals(0, $reg['valor_ajuste'], 'Valor do ajuste, index 7');
        $this->assertEquals('000009', $reg['sequencial_registro'], 'Sequencial do registro, index 7');
    }

    public function testProcessarRetornoCarteira17SemLancamento()
    {
        $conteudoArquivo = file_get_contents('tests/resources/CBR643562303201713947.ret');
        $bb = new BancoBrasil();
        $bb->processarRetorno($conteudoArquivo);
        $this->assertEquals('3680', $bb->agencia(), 'Agência');
        $this->assertEquals('3', $bb->agenciaDV(), 'DV da agência');
        $this->assertEquals('00015399', $bb->contaCorrente(), 'Conta corrente');
        $this->assertEquals('0', $bb->contaCorrenteDV(), 'DV da conta corrente');
        $this->assertEquals('2962087', $bb->convenio(), 'Convênio');
        $this->assertEquals('ACBDEFGH PAYMENT SECURITY LTDA', $bb->nomeCedente(), 'Nome do cedente');

        // Registros detalhe
        $registros_detalhe = $bb->registrosDetalhe();
        $this->assertEquals(1, count($registros_detalhe), 'Quantidade registros detalhe');

        $reg = $registros_detalhe[0];
        $this->assertEquals('3680', $reg['agencia'], 'Agência');
        $this->assertEquals('3', $reg['agencia_dv'], 'DV da agência');
        $this->assertEquals('00015399', $reg['conta'], 'Conta corrente');
        $this->assertEquals('0', $reg['conta_dv'], 'DV da conta corrente');
        $this->assertEquals('2962087', $reg['convenio'], 'Convênio');
        $this->assertEquals('29620870000000002', $reg['nosso_numero'], 'Nosso número');
        $this->assertEquals('0000000002', $reg['numero'], 'Número boleto');
        $this->assertEquals('29', $reg['natureza_recebimento'], 'Natureza do recebimento');
        $this->assertEquals('RECUSA', $reg['tipo_ocorrencia'], 'Tipo da ocorrência');
        $this->assertEquals('Endereço não informado', $reg['ocorrencia'], 'Ocorrência');
        $this->assertEquals('019', $reg['variacao_carteira'], 'Variação carteira');
        $this->assertEquals(0.00, $reg['taxa_desconto'], 'Desconto');
        $this->assertEquals(0.00, $reg['taxa_iof'], 'IOF');
        $this->assertEquals('17', $reg['carteira'], 'Carteira');
        $this->assertEquals('2017-03-30', $reg['data_vencimento'], 'Data vencimento');
        $this->assertEquals(1.0, $reg['valor_titulo'], 'Valor do título');
        $this->assertEquals('001', $reg['banco_recebedor'], 'Código do banco recebedor');
        $this->assertEquals('0000', $reg['agencia_recebedor'], 'Prefixo da agência recebedora');
        $this->assertEquals(' ', $reg['agencia_dv_recebedor'], 'DV prefixo recebedora');
        $this->assertEquals('01', $reg['especie_titulo'], 'Espécie do título');
        $this->assertEquals(0, $reg['valor_tarifa'], 'Valor da tarifa');
        $this->assertEquals(0, $reg['outras_despesas'], 'Outras despesas');
        $this->assertEquals(0, $reg['juros_desconto'], 'Juros do desconto');
        $this->assertEquals(0, $reg['iof_desconto'], 'IOF do desconto');
        $this->assertEquals(0, $reg['valor_abatimento'], 'Valor do abatimento');
        $this->assertEquals(0, $reg['desconto'], 'Desconto concedido  (diferença entre valor do título e valor recebido)');
        $this->assertEquals('', $reg['data_credito'], 'Data do crédito (DDMMAA)');
        $this->assertEquals('', $reg['data_liquidacao'], 'Data liquidação');
        $this->assertEquals(0, $reg['valor_pagto'], 'Valor recebido (valor recebido parcial)');
        $this->assertEquals(0, $reg['juros_mora'], 'Juros de mora');
        $this->assertEquals(0, $reg['outros_recebimentos'], 'Outros recebimentos');
        $this->assertEquals(0, $reg['abatimento_nao_aproveitado'], 'Abatimento não aproveitado pelo sacado');
        $this->assertEquals(0, $reg['valor_lancamento'], 'Valor do lançamento');
        $this->assertEquals('sem lançamento', $reg['indicativo_lancamento'], 'Indicativo de débito/crédito');
        $this->assertEquals('0', $reg['indicativo_valor'], 'Indicador de valor');
        $this->assertEquals(0, $reg['valor_ajuste'], 'Valor do ajuste');
    }

    public function testProcessarRetornoManutencaoTituloVencido()
    {
        $conteudoArquivo = file_get_contents('tests/resources/CBR643_man_titulo_vencido.ret');
        $bb = new BancoBrasil();
        $bb->processarRetorno($conteudoArquivo);

        // Registros detalhe
        $registros_detalhe = $bb->registrosDetalhe();
        $this->assertEquals(3, count($registros_detalhe), 'Quantidade registros detalhe');

        $reg = $registros_detalhe[0];
        $this->assertEquals('29620870000000003', $reg['nosso_numero'], 'Nosso número');
        $this->assertEquals(BancoBrasil::MANUTENCAO_TITULO_VENCIDO, $reg['tipo_ocorrencia'], 'Tipo da ocorrência');
        $this->assertEquals(BancoBrasil::DESC_MANUTENCAO_TITULO_VENCIDO, $reg['ocorrencia'], 'Ocorrência');
        $this->assertEquals(0.5, $reg['valor_tarifa'], 'Valor da tarifa');

        $reg = $registros_detalhe[1];
        $this->assertEquals('29620870000000005', $reg['nosso_numero'], 'Nosso número');
        $this->assertEquals(BancoBrasil::MANUTENCAO_TITULO_VENCIDO, $reg['tipo_ocorrencia'], 'Tipo da ocorrência');
        $this->assertEquals(BancoBrasil::DESC_MANUTENCAO_TITULO_VENCIDO, $reg['ocorrencia'], 'Ocorrência');
        $this->assertEquals(67890.53, $reg['valor_tarifa'], 'Valor da tarifa');
    }
}
