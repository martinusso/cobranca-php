<?php

namespace Cobranca\Boleto;

use Cobranca\Utils\CodigoDeBarras;
use Cobranca\Utils\LinhaDigitavel;
use Cobranca\Utils\FatorVencimento;

/**
* Boleto
*/
abstract class AbstractBoleto
{
  protected $valor;
  protected $dataVencimento;
  protected $numero;
  protected $conta;
  protected $pagador;
  protected $avalista;
  protected $codigo_barras = null;

  public function __construct($valor, $dataVencimento, $numero, $conta, $pagador, $avalista)
  {
    $this->valor = $valor;
    $this->dataVencimento = $dataVencimento;
    $this->numero = $numero;
    $this->conta = $conta;
    $this->pagador = $pagador;
    $this->avalista = $avalista;
    return $this;
  }

  abstract public function banco();
  abstract public function campoLivre();
  /**
   * Código de Barras
   *
   * @return string
   */
  public function codigoBarras()
  {
    if (!$this->codigo_barras) {
      $this->codigo_barras = CodigoDeBarras::create(
          $this->banco(),
          $this->codigoMoeda(),
          $this->fatorVencimento(),
          $this->valor,
          $this->campoLivre()
      );
    }
    return $this->codigo_barras;
  }

  /**
   * Retorna a data do documento
   *
   * @return string
   */
  public function dataDocumento()
  {
    return date('Y-m-d');
  }

  /**
   * Retorna a data do documento
   *
   * @return string
   */
  public function dataProcessamento()
  {
    return date('Y-m-d');
  }

  /**
   * Retorna o código da moeda (9 - Real)
   *
   * @return string
   */
  public function fatorVencimento()
  {
    return FatorVencimento::calcular($this->dataVencimento);
  }

  /**
   *  Linha Digitável – Representação Numérica do Código de Barras
   *
   * @return string
   */
  public function linhaDigitavel()
  {
    return LinhaDigitavel::create($this->codigoBarras());
  }

  /**
   * Nosso Número
   * Código de controle que permite ao Banco e ao beneficiário identificar os dados da cobrança que deu origem ao boleto de pagamento.
   *
   * @return string
   */
  abstract public function nossoNumero();

  /**
   * Nosso Número sem o DV (Dígito Verificador)
   *
   * @return string
   */
  abstract public function nossoNumeroSemDV();

  /**
   * Retorna o código da moeda (9 - Real)
   *
   * @return string
   */
  public function codigoMoeda()
  {
    return '9';
  }

  /**
   * Retorna a sigla de identificação da moeda (R$ - Real)
   *
   * @return string
   */
  public function especieMoeda()
  {
    return 'R$';
  }

  /**
   * Retorna o valor do boleto
   *
   * @return float
   */
  public function valor()
  {
    return $this->valor;
  }

  abstract public function localPagamento();
}
