<?php

class CalculadoraImc
{
    private Usuario $usuario;

    public function __construct(Usuario $usuario)
    {
        $this->usuario = $usuario;
    }

    public function calcular(): ?float
    {
        $idade = $this->usuario->getIdadeAtual();
        if ($idade >= 10) {
            return $this->usuario->getPeso() / ($this->usuario->getAltura() * $this->usuario->getAltura());
        } else {
            return null;
        }
    }

    public function classificarPorFaixaEtariaSexo(): string
    {
        $idade = $this->usuario->getIdadeAtual();
        $sexo = $this->usuario->getSexo()->value;
        $imc = $this->calcular();

        if ($idade >= 140) {
            return 'Matusalém';
        } else if ($idade >= 20) {
            return ClassificacaoImcEnum::classifica($imc);
        } else {
            if ($idade >= 10 && $idade < 20) {
                $imc = $this->calcular();

                if ($sexo === 'Feminino') {
                    return $this->classificarPorIdadePercentilFeminino($idade, $imc);
                } elseif ($sexo === 'Masculino') {
                    return $this->classificarPorIdadePercentilMasculino($idade, $imc);
                }
            } else {
                return 'O cálculo deve ser feito com pessoas com mais de 10 anos de idade.';
            }
        }
    }

    private function classificarPorIdadePercentilFeminino(int $idade, float $imc): string
    {
        $percentis = [
            10 => [14.23, 20.19],
            11 => [14.6, 21.28],
            12 => [14.98, 22.17],
            13 => [15.36, 23.08],
            14 => [15.67, 23.88],
            15 => [16.01, 24.29],
            16 => [16.37, 24.74],
            17 => [16.59, 25.23],
            18 => [16.71, 25.56],
            19 => [16.87, 25.85],
        ];

        return $this->classificarPorPercentis($percentis[$idade][0], $percentis[$idade][1], $imc);
    }

    private function classificarPorIdadePercentilMasculino(int $idade, float $imc): string
    {
        $percentis = [
            10 => [14.42, 19.6],
            11 => [14.83, 20.35],
            12 => [15.24, 21.12],
            13 => [15.73, 21.93],
            14 => [16.18, 22.77],
            15 => [16.59, 23.63],
            16 => [17.01, 24.45],
            17 => [17.31, 25.28],
            18 => [17.54, 25.95],
            19 => [17.8, 26.36],
        ];

        return $this->classificarPorPercentis($percentis[$idade][0], $percentis[$idade][1], $imc);
    }

    private function classificarPorPercentis(float $percentilBaixo, float $percentilAlto, float $imc): string
    {
        if ($imc < $percentilBaixo) {
            return 'Baixo Peso';
        } elseif ($imc >= $percentilBaixo && $imc < $percentilAlto) {
            return 'Adequado ou Eutrófico';
        } else {
            return 'Sobrepeso';
        }
    }
}
