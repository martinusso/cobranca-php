<?php

namespace Cobranca\Tests\Remessa;

use Cobranca\Pessoa;
use Cobranca\Remessa\Pagamento;

final class PagamentoTest extends \Cobranca\Tests\AbstractTestCase
{
    public function testMensagemAvalistaPessoaFisica()
    {
        $expected = 'DAENERYS STORMBORN OF THE CPF43378343893';
        $avalista = ['nome' => 'Daenerys Stormborn of the House Targaryen, First of Her Name, the Unburnt',
            'documento' => '433.783.438-93'];
        $pagamento = $this->novoPagamento($avalista);
        $mensagem = $pagamento->mensagemAvalista();
        $this->assertEquals($expected, $mensagem);
        $this->assertEquals(40, strlen($mensagem));

        $this->assertEquals(1, $pagamento->valor());
        $this->assertEquals(new \DateTime('1984-09-11'), $pagamento->dataVencimento());
        $this->assertEquals('1', $pagamento->numero());
        $this->assertEquals(45, $pagamento->diasProtesto());
        $this->assertEquals(2, $pagamento->percentualMoraAoMes());
        $this->assertEquals(3, $pagamento->percentualMulta());
        $this->assertEquals('ACME Corporation', $pagamento->pagador()->nome());
    }

    public function testMensagemAvalistaPessoaJuridica()
    {
        $expected = 'AMERICAN COMPANY MAKE CNPJ99999999000191';
        $avalista = [
            'nome' => 'American Company Makes Evertything',
            'documento' => '99.999.999/0001-91'];
        $pagamento = $this->novoPagamento($avalista);
        $mensagem = $pagamento->mensagemAvalista();
        $this->assertEquals($expected, $mensagem);
        $this->assertEquals(40, strlen($mensagem));
    }

    public function testMensagemAvalistaEmBranco()
    {
        $pagamento = $this->novoPagamento([]);
        $this->assertEquals('', $pagamento->mensagemAvalista());

        $avalista = [
            'nome' => 'American Company Makes Evertything',
            'documento' => '12345'];
        $pagamento = $this->novoPagamento($avalista);
        $this->assertEquals('', $pagamento->mensagemAvalista());
    }

    public function testMensagemAvalistaSomenteNome()
    {
        $avalista = [
            'nome' => 'American Company Makes Evertything'];
        $pagamento = $this->novoPagamento($avalista);
        $this->assertEquals('American Company Makes Evertything', $pagamento->mensagemAvalista());
    }

    private function novoPagamento(array $avalista)
    {
        $pagador = [
            'documento' => '99.999.999/0001-91',
            'nome' => 'ACME Corporation',
            'endereco' => 'Rua',
            'bairro' => 'Centro',
            'cep' => '29.000-000',
            'cidade' => 'Vitória',
            'uf' => 'ES'
        ];

        return new Pagamento('registro', 1, new \DateTime('1984-09-11'), '1', '1234567890', 45, 2, 3, $pagador, $avalista);
    }
}
