<?php

namespace Cobranca;

/*
 * Pessoa
 */
class Pessoa
{
    private $nome;
    private $documento;
    private $endereco;
    private $bairro;
    private $cep;
    private $uf;
    private $cidade;

    /**
     * Construtor
     *
     * @param string $nome
     * @param string $documento
     * @param string $endereco
     * @param string $bairro
     * @param string $cep
     * @param string $cidade
     * @param string $uf
     */
    public function __construct($nome, $documento, $endereco = '', $bairro = '', $cidade = '', $uf = '', $cep = '')
    {
        $this->nome = $nome;
        $this->documento = preg_replace("/[^0-9]/", "", $documento);
        $this->endereco = $endereco;
        $this->bairro = $bairro;
        $this->cep = preg_replace("/[^0-9]/", "", $cep);
        $this->cidade = $cidade;
        $this->uf = $uf;
    }

    /**
     * Retorna o Bairro
     *
     * @return string
     */
    public function bairro()
    {
        return $this->bairro;
    }

    /**
     * Retorna o CEP
     *
     * @return string
     */
    public function cep()
    {
        return $this->cep;
    }

    /**
     * Retorna a cidade
     *
     * @return string
     */
    public function cidade()
    {
        return $this->cidade;
    }

    /**
     * Retorna o documento (CPF ou CNPJ)
     *
     * @return string
     */
    public function documento()
    {
        return $this->documento;
    }

    /**
     * Retorna o endereço
     *
     * @return string
     */
    public function endereco()
    {
        return $this->endereco;
    }

    /**
     * Retorna o nome
     *
     * @return string
     */
    public function nome()
    {
        return $this->nome;
    }

    /**
     * Retorna o tipo de inscrição
     * 00 - ISENTO
     * 01 - CPF
     * 02 - CNPJ
     *
     * @return string
     */
    public function tipoInscricao()
    {
      switch (strlen($this->documento)) {
        case 11:
          return '01';
        case 14:
          return '02';
        default:
          return '00';
      }
    }

    /**
     * Retorna a UF
     *
     * @return string
     */
    public function uf()
    {
        return $this->uf;
    }
}
