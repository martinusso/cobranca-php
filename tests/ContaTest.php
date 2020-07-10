<?php

namespace Cobranca\Tests\Boleto;

use Cobranca\Conta;
use Cobranca\Pessoa;

final class ContaTest extends \Cobranca\Tests\AbstractTestCase
{
    private $banco = '001';
    private $agencia = '1234';
    private $contaCorrente = '123456';
    private $carteira = '17';
    private $variacao = '019';
    private $convenio = '1234567';
    private $especie_titulo = '01';
    private $aceite = 'S';
    private $sequencial_remessa = 1;

    protected function setUp()
    {
        $this->beneficiario = new Pessoa('Beneficiário', '00000000000191');
    }

    public function testConta()
    {
        $conta = new Conta(
            $this->banco,
            $this->agencia,
            $this->contaCorrente,
            $this->carteira,
            $this->variacao,
            $this->convenio,
            $this->beneficiario,
            $this->especie_titulo,
            $this->aceite,
            $this->sequencial_remessa);

        $this->assertEquals('A', $conta->aceite());
        $this->assertEquals($this->agencia, $conta->agencia());
        $this->assertEquals($this->banco, $conta->banco());
        $this->assertEquals($this->contaCorrente, $conta->contaCorrente());
        $this->assertEquals($this->carteira, $conta->carteira());
        $this->assertEquals($this->variacao, $conta->variacao());
        $this->assertEquals($this->convenio, $conta->convenio());
        // $this->assertEquals('A', $conta->beneficiario());
        $this->assertEquals($this->especie_titulo, $conta->especieTitulo());
        $this->assertEquals($this->sequencial_remessa, $conta->sequencialRemessa());
    }

    public function testAceiteNulo()
    {
        $conta = new Conta(
            $this->banco,
            $this->agencia,
            $this->contaCorrente,
            $this->carteira,
            $this->variacao,
            $this->convenio,
            $this->beneficiario,
            $this->especie_titulo,
            null,
            $this->sequencial_remessa);

        $this->assertEquals('N', $conta->aceite());
    }

    public function testAceite()
    {
        $conta = new Conta(
            $this->banco,
            $this->agencia,
            $this->contaCorrente,
            $this->carteira,
            $this->variacao,
            $this->convenio,
            $this->beneficiario,
            $this->especie_titulo,
            $this->aceite,
            $this->sequencial_remessa);
        $this->assertEquals('A', $conta->aceite());

        $conta = new Conta(
            $this->banco,
            $this->agencia,
            $this->contaCorrente,
            $this->carteira,
            $this->variacao,
            $this->convenio,
            $this->beneficiario,
            $this->especie_titulo,
            'N',
            $this->sequencial_remessa);

        $this->assertEquals('N', $conta->aceite());
    }

    public function testEspecieTituloNulo()
    {
        $conta = new Conta(
            $this->banco,
            $this->agencia,
            $this->contaCorrente,
            $this->carteira,
            $this->variacao,
            $this->convenio,
            $this->beneficiario,
            null,
            $this->aceite,
            $this->sequencial_remessa);

        $this->assertEquals(null, $conta->especieTitulo());
    }

    public function testEspecieTitulo()
    {
        $conta = new Conta(
            $this->banco,
            $this->agencia,
            $this->contaCorrente,
            $this->carteira,
            $this->variacao,
            $this->convenio,
            $this->beneficiario,
            $this->especie_titulo,
            $this->aceite,
            $this->sequencial_remessa);

        $this->assertEquals('01', $conta->especieTitulo());
    }

    /**
     * @expectedException InvalidArgumentException
     */
    public function testDeveGerarExcecaoParaEspecieTituloInvalido()
    {
        $conta = new Conta(
            $this->banco,
            $this->agencia,
            $this->contaCorrente,
            $this->carteira,
            $this->variacao,
            $this->convenio,
            $this->beneficiario,
            '42',
            $this->aceite,
            $this->sequencial_remessa);
    }
}
