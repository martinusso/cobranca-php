<?php

namespace Cobranca\Tests\Remessa\CNAB400;

use Cobranca\Utils\Functions;
use Cobranca\Pessoa;
use Cobranca\Conta;
use Cobranca\Remessa\Pagamento;
use Cobranca\Remessa\CNAB400\BancoBrasil;

final class BancoBrasilTest extends \Cobranca\Tests\AbstractTestCase
{
    public function testCNAB400()
    {
        $expectedHeader = '01REMESSA01COBRANCA       40428000619000000000ACME CORPORATION              001BANCODOBRASIL  ' .
            date('dmy') .
            '0000001                      1234567' .
            Functions::brancos('', 258) .
            '000001';
        $expectedDetalheAntesData = '70299999999000191404280006190001234567                         123456700000001230000       0190000000     ' .
            '1801000000012316021700000000001990010000 00A';
        $expectedDetalheDepoisData = '000000000000000000000000000000000000000000000000000000000000000200000000000191' .
            'NOME DO SACADO                          ENDERECO DO SACADO                      BAIRRO      29315732CACHOEIRO DE ITES' .
            '                                        30 000002';

        $expectedTrailer = '9' . Functions::brancos('', 393) . '000004';

        $remessa = new BancoBrasil($this->novaConta('18', '1234567'));
        $remessa->addPagamento($this->novoPagamento(1.99, new \DateTime('2017-02-16'), '123'));
        $strings = $remessa->strings();
        $linhas = explode("\n", $strings);
        $this->assertEquals(4, count($linhas));

        $this->assertEquals($expectedHeader, $linhas[0], 'Registro header inválido');
        $this->assertEquals(400, strlen(utf8_decode($linhas[0])), 'Tamanho do registro header inválido');

        $this->assertContains($expectedDetalheAntesData, $linhas[1], 'Registro detalhe inválido antes da data de emissão');
        $this->assertContains($expectedDetalheDepoisData, $linhas[1], 'Registro detalhe inválido depois da data de emissão');
        $this->assertEquals(400, strlen(utf8_decode($linhas[1])), 'Tamanho do registro detalhe inválido' . $linhas[1]);

        $this->assertContains('5992160217000000000100', $linhas[2], 'Registro detalhe de multa inválido');

        $this->assertEquals($expectedTrailer, $linhas[3], 'Registro Trailer inválido');
        $this->assertEquals(400, strlen(utf8_decode($linhas[3])), 'Registro Trailer inválido');
        $this->assertEquals('REMESSA_1.rem', $remessa->nomeArquivo(), 'Nome do arquivo');
    }

    public function testCNAB400ComAvalista()
    {
        $remessa = new BancoBrasil($this->novaConta('18', '1234567'));
        $avalista = [
            'nome' => 'Nome do Avalista',
            'documento' => '99.999.999/0001-91'
        ];
        $remessa->addPagamento($this->novoPagamento(1.99, new \DateTime('2017-02-16'), '123', 1, 1, $avalista));
        $this->assertEquals(1, count($remessa->pagamentos()));
    }

    public function testCNAB400TipoCobrancaModalidadeSimples()
    {
        $remessa = new BancoBrasil($this->novaConta('17', '1234567'));
        $avalista = [
            'nome' => 'Nome do Avalista',
            'documento' => '99.999.999/0001-91'
        ];
        $remessa->addPagamento($this->novoPagamento(1.99, new \DateTime('2017-02-16'), '123'));
        $strings = $remessa->strings();
        $linhas = explode("\n", $strings);
        $this->assertEquals(4, count($linhas));
    }

    private function novaConta($carteira, $convenio)
    {
        $agencia = '4042';
        $contaCorrente = '61900';
        $beneficiario = new Pessoa('ACME Corporation', '99999999000191', 'Rua Ada Lovelace, 42', 'Turing', 'Vitória', 'ES', '29060-100');

        return new Conta('001', $agencia, $contaCorrente, $carteira, '019', $convenio, $beneficiario, null, 'A', 1);
    }

    private function novoPagamento($valor, $data_vencimento, $numero, $nosso_numero = null,
        $dias_protesto = 30,
        $percentual_mora_ao_mes = 1,
        $percentual_multa = 1,
        $avalista = []
    ) {
        empty($nosso_numero) && $nosso_numero = $numero;
        $pagador = [
            'documento' => '00000000000191',
            'nome' => 'Nome do sacado',
            'endereco' => 'Endereço do sacado',
            'bairro' => 'Bairro',
            'cep' => '29315-732',
            'cidade' => 'Cachoeiro de Itapemirim',
            'uf' => 'ES'

        ];
        return new Pagamento('registro', $valor, $data_vencimento, $numero, $nosso_numero,
            $dias_protesto,
            $percentual_mora_ao_mes,
            $percentual_multa,
            $pagador);
    }
}
