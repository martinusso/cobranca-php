## Cobrança PHP

[![Build Status](https://travis-ci.org/martinusso/cobranca-php.svg?branch=master)](https://travis-ci.org/martinusso/cobranca-php)

## Requisitos

- PHP 7.^
- gd


## Funcionalidades

|         Bancos         |  Carteiras  |  Retorno  |  Remessa  |  Documentações  |
|------------------------|-------------|-----------|-----------|-----------------|
| 001 - Banco do Brasil  | Todas as carteiras presentes na documentação | CNAB400/CBR643 | CNAB400/CBR641 | http://www.bb.com.br/docs/pub/emp/empl/dwn/Doc5175Bloqueto.pdf |


## Como usar


## Informações do boleto

Boleto:
  Valor do boleto
  Número sequencial utilizado para identificar o boleto * Atenção ao banco de dados, pois provavelmente necessário gerar um valor incrementador para cada conta.
  Data em que foi emitido o boleto :data_documento
  Data de processamento do boleto, geralmente igual a data_documento
  Data de vencimento do boleto

Configuração conta:
  Banco
  Cedente
  Sacado
  Número do convênio/contrato do cliente junto ao banco emissor
  Carteira utilizada
  Variacao da carteira (opcional para a maioria dos bancos)
  Agencia
  Conta corrente
  Tipo do documento/Espécie do documento (exemplo: DM = Duplicata Mercantil)
  Aceite: Informa se o banco deve aceitar o boleto após o vencimento ou não( S ou N, quase sempre S)
  Local pagamento: Informação sobre onde o sacado podera efetuar o pagamento
  Multa atraso
  juros dia/mês
  dias protesto
  Informações de Responsabilidade do Beneficiário

Cedente:
  Nome do proprietario da conta corrente
  Documento do proprietario da conta corrente (CPF ou CNPJ)
  Opcional:	Endereço da pessoa que envia o boleto

Sacado:
  Nome da pessoa que receberá o boleto
  Opcional: Documento da pessoa que receberá o boleto
  Opcional: Endereco da pessoa que receberá o boleto

Opcional:

  Nome do avalista
  Documento do avalista

Valores fixos:
  Tipo de moeda utilizada (Real(R$) e igual a 9)
  Símbolo da moeda utilizada (R$ no brasil)

Retorno:
  Layout do retorno

Remessa:
  Layout da remessa
  sequencial remessa (num. sequencial que nao pode ser repetido nem zerado)
  aceite (A = ACEITO/N = NAO ACEITO)
