<?php

namespace Cobranca\Tests\Boleto;

use Cobranca\Pessoa;

final class PessoaTest extends \Cobranca\Tests\AbstractTestCase
{
    private $nome = 'Anakin Skywalker';
    private $documento = '99999999000191';
    private $endereco = 'Capital Secreta do Mundo';
    private $bairro = 'Centro';
    private $cidade = 'Cachoeiro';
    private $uf = 'ES';
    private $cep = '12.234-567';
    private $cep_sem_mascara = '12234567';

    public function testNovaPessoa()
    {
        $pessoa = new Pessoa($this->nome, $this->documento);
        $this->assertEquals($this->nome, $pessoa->nome());
        $this->assertEquals($this->documento, $pessoa->documento());
        $this->assertEquals(null, $pessoa->endereco());
        $this->assertEquals(null, $pessoa->bairro());
        $this->assertEquals(null, $pessoa->cidade());
        $this->assertEquals(null, $pessoa->uf());
        $this->assertEquals(null, $pessoa->cep());
    }

    public function testTipoInscricao()
    {

        // **
        //  * Retorna o tipo de inscrição
        //  * 00 - ISENTO
        //  * 01 - CPF
        //  * 02 - CNPJ
        //  *
        //  * @return string
        //  */
        // public function tipoInscricao()
        // {
        //   switch (strlen($this->documento)) {
        //     case 11:
        //       return '01';
        //     case 14:
        //       return '02';
        //     default:
        //       return '00';
        //   }
        // }
        //
        $pessoa = new Pessoa($this->nome, '114.783.581-06');
        $this->assertEquals('01', $pessoa->tipoInscricao());

        $pessoa = new Pessoa($this->nome, '99.999.999/0001-91');
        $this->assertEquals('02', $pessoa->tipoInscricao());

        $pessoa = new Pessoa($this->nome, '2423238');
        $this->assertEquals('00', $pessoa->tipoInscricao());
    }

    public function testNovaPessoaComEnderecoCompleto()
    {
        $pessoa = new Pessoa($this->nome, $this->documento, $this->endereco, $this->bairro, $this->cidade, $this->uf, $this->cep);
        $this->assertEquals($this->nome, $pessoa->nome());
        $this->assertEquals($this->documento, $pessoa->documento());
        $this->assertEquals($this->endereco, $pessoa->endereco());
        $this->assertEquals($this->bairro, $pessoa->bairro());
        $this->assertEquals($this->cidade, $pessoa->cidade());
        $this->assertEquals($this->uf, $pessoa->uf());
        $this->assertEquals($this->cep_sem_mascara, $pessoa->cep());
    }

    public function testNovaPessoaAteEndereco()
    {
        $pessoa = new Pessoa($this->nome, $this->documento, $this->endereco);
        $this->assertEquals($this->nome, $pessoa->nome());
        $this->assertEquals($this->documento, $pessoa->documento());
        $this->assertEquals($this->endereco, $pessoa->endereco());
        $this->assertEquals(null, $pessoa->bairro());
        $this->assertEquals(null, $pessoa->cidade());
        $this->assertEquals(null, $pessoa->uf());
        $this->assertEquals(null, $pessoa->cep());
    }

    public function testNovaPessoaAteBairro()
    {
        $pessoa = new Pessoa($this->nome, $this->documento, $this->endereco, $this->bairro);
        $this->assertEquals($this->nome, $pessoa->nome());
        $this->assertEquals($this->documento, $pessoa->documento());
        $this->assertEquals($this->endereco, $pessoa->endereco());
        $this->assertEquals($this->bairro, $pessoa->bairro());
        $this->assertEquals(null, $pessoa->cidade());
        $this->assertEquals(null, $pessoa->uf());
        $this->assertEquals(null, $pessoa->cep());
    }

    public function testNovaPessoaAteCidade()
    {
        $pessoa = new Pessoa($this->nome, $this->documento, $this->endereco, $this->bairro, $this->cidade);
        $this->assertEquals($this->nome, $pessoa->nome());
        $this->assertEquals($this->documento, $pessoa->documento());
        $this->assertEquals($this->endereco, $pessoa->endereco());
        $this->assertEquals($this->bairro, $pessoa->bairro());
        $this->assertEquals($this->cidade, $pessoa->cidade());
        $this->assertEquals(null, $pessoa->uf());
        $this->assertEquals(null, $pessoa->cep());
    }

    public function testNovaPessoaAteUF()
    {
        $pessoa = new Pessoa($this->nome, $this->documento, $this->endereco, $this->bairro, $this->cidade, $this->uf);
        $this->assertEquals($this->nome, $pessoa->nome());
        $this->assertEquals($this->documento, $pessoa->documento());
        $this->assertEquals($this->endereco, $pessoa->endereco());
        $this->assertEquals($this->bairro, $pessoa->bairro());
        $this->assertEquals($this->cidade, $pessoa->cidade());
        $this->assertEquals($this->uf, $pessoa->uf());
        $this->assertEquals(null, $pessoa->cep());
    }
}
