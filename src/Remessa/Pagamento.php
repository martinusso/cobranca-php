<?php

namespace Cobranca\Remessa;

use Cobranca\Utils\Functions;
use Cobranca\Remessa;
use Cobranca\Pessoa;

class Pagamento
{
    private $comando;
    private $valor;
    private $data_vencimento;
    private $numero;
    private $nosso_numero;
    private $percentual_mora_ao_mes;
    private $dias_protesto;
    private $percentual_multa;
    private $pagador;
    private $avalista;

    /**
    * Construtor
    *
    * @param string $comando
    * @param string $valor
    * @param string $data_vencimento
    * @param string $numero
    * @param string $nosso_numero
    * @param string $dias_protesto
    * @param string $percentual_mora_ao_mes
    * @param string $percentual_multa
    * @param array $pagador Pagador/Sacado
    * @param array $avalista
    */
    public function __construct($comando,
        $valor,
        $data_vencimento,
        $numero,
        $nosso_numero,
        $dias_protesto,
        $percentual_mora_ao_mes,
        $percentual_multa,
        array $pagador,
        array $avalista = []
    ) {
        $this->comando = $comando;
        $this->valor = $valor;
        $this->data_vencimento = $data_vencimento;
        $this->numero = $numero;
        $this->nosso_numero = $nosso_numero;
        $this->dias_protesto = $dias_protesto;
        $this->percentual_mora_ao_mes = $percentual_mora_ao_mes;
        $this->percentual_multa = $percentual_multa;

        $this->setPagador($pagador);
        $this->avalista = $avalista;
    }

    public function avalista()
    {
        return $this->avalista;
    }

    /**
    * Mensagem Avalista
    * Para CNPJ
    *  Posição 352 à 372 - Preencher com o nome do Sacador/Avalista.
    *  Posição 373 - Preencher com "espaço"
    *  Posição 374 à 377 - Preencher com o literal "CNPJ"
    *  Posição 378 à 391 - Preencher com o número do CNPJ do Sacador/Avalista
    * Para CPF
    *  Posição 352 à 376 - Preencher com o nome do Sacador/Avalista
    *  Posição 377 - Preencher com "espaço"
    *  Posição 378 à 380 - Preencher com o literal "CPF"
    *  Posição 381 à 391 - Preencher com o número do CPF do Sacador/Avalista
    *
    * @return Pessoa Retorna o avalista
    */
    public function mensagemAvalista()
    {
        $nome = isset($this->avalista['nome']) ? $this->avalista['nome'] : '';

        if (isset($this->avalista['documento'])) {
            $documento = preg_replace("/[^0-9]/", "", $this->avalista['documento']);
            switch (strlen($documento)) {
                case 11:
                    return Functions::brancos($nome, 25) . ' CPF' . $documento;
                    break;
                case 14:
                    return Functions::brancos($nome, 21) . ' CNPJ' . $documento;
                    break;
                default:
                    return "";
                    break;
        }
        } else {
            return $nome;
        }
    }

    /**
    * Comando
    *
    * @return string
    */
    public function comando()
    {
        return $this->comando;
    }

    /**
    * Nosso número
    *
    * @return string
    */
    public function nossoNumero()
    {
        return preg_replace("/[^0-9]/", "", $this->nosso_numero);
    }


    /**
    * Valor
    *
    * @return string
    */
    public function valor()
    {
        return $this->valor;
    }

    /**
    * Data de vencimento
    *
    * @return string
    */
    public function dataVencimento()
    {
        return $this->data_vencimento;
    }

    /**
    * Nosso número
    *
    * @return string
    */
    public function numero()
    {
        return $this->numero;
    }

    /**
    * Percentual mora ao mês
    *
    * @return string
    */
    public function percentualMoraAoMes()
    {
        return $this->percentual_mora_ao_mes;
    }

    /**
    * Dias protesto
    *
    * @return string
    */
    public function diasProtesto()
    {
        return $this->dias_protesto;
    }

    /**
    * Percentual multa
    *
    * @return string
    */
    public function percentualMulta()
    {
        return $this->percentual_multa;
    }

    /**
    * Pagador
    *
    * @return Pessoa
    */
    public function pagador()
    {
        return $this->pagador;
    }

    private function setPagador($pagador)
    {
        $this->pagador = new Pessoa(
            $pagador['nome'],
            $pagador['documento'],
            $pagador['endereco'],
            $pagador['bairro'],
            $pagador['cidade'],
            $pagador['uf'],
            $pagador['cep']
        );
    }
}
