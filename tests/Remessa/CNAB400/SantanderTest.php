<?php

namespace Cobranca\Tests\Remessa\CNAB400;

use Cobranca\Utils\Functions;
use Cobranca\Pessoa;
use Cobranca\Conta;
use Cobranca\Remessa\Pagamento;
use Cobranca\Remessa\CNAB400\Santander;

final class SantanderTest extends \Cobranca\Tests\AbstractTestCase
{
    public function testCNAB400()
    {
        $expectedHeader = '01REMESSA01COBRANCA       17777751042700080112ACME CORPORATION              033SANTANDER      ' .
            date('dmy') .
            '0000000000000000                    ' .
            Functions::brancos('', 255) .
            '000000001';
        $expectedDetalheAntesData = '70299999999000191404280006190001234567                         123456700000001230000       0190000000     ' .
            '1801000000012316021700000000001990010000 00A';
        $expectedDetalheDepoisData = '000000000000000000000000000000000000000000000000000000000000000200000000000191' .
            'NOME DO SACADO                          ENDERECO DO SACADO                      BAIRRO      29315732CACHOEIRO DE ITES' .
            '                                        30 000002';

        $expectedTrailer = '9000001000000000019900000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000003';

        $remessa = new Santander(
            $this->novaConta('18', '1234567'),
            ['codigo_transmissao' => '17777751042700080112']
        );
        $remessa->addPagamento($this->novoPagamento(1.99, new \DateTime('2017-02-16'), '123'));
        $strings = $remessa->strings();
        $linhas = explode("\n", $strings);
        $this->assertEquals(3, count($linhas));

        $this->assertEquals($expectedHeader, $linhas[0], 'Registro header inválido');
        $this->assertEquals(400, strlen(utf8_decode($linhas[0])), 'Tamanho do registro header inválido');

        // $this->assertContains($expectedDetalheAntesData, $linhas[1], 'Registro detalhe inválido antes da data de emissão');
        // $this->assertContains($expectedDetalheDepoisData, $linhas[1], 'Registro detalhe inválido depois da data de emissão');
        $this->assertEquals(400, strlen(utf8_decode($linhas[1])), 'Tamanho do registro detalhe inválido' . $linhas[1]);

        $this->assertEquals($expectedTrailer, $linhas[2], 'Registro Trailer inválido');
        $this->assertEquals(400, strlen(utf8_decode($linhas[2])), 'Registro Trailer inválido');
        $this->assertEquals('REMESSA_1.rem', $remessa->nomeArquivo(), 'Nome do arquivo');
    }

    private function novaConta($carteira, $convenio)
    {
        $agencia = '4042';
        $contaCorrente = '130037589';
        $beneficiario = new Pessoa(
            'ACME Corporation',
            '99999999000191',
            'Rua Ada Lovelace,
            42',
            'Turing',
            'Vitória',
            'ES',
            '29060-100'
        );

        return new Conta(
            '001',
            $agencia,
            $contaCorrente,
            $carteira,
            '019',
            $convenio,
            $beneficiario,
            null,
            'A',
            1
        );
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
