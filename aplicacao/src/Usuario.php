<?php

class Usuario{
    private string $nome;
    private DateTimeInterface $dataNascimento;
    private float $peso;
    private float $altura;
    private SexoEnum $sexo;
    
    public function __construct(string $nome, DateTimeInterface $dataNascimento, float $peso, float $altura, SexoEnum $sexo){
        $this->nome=$nome;
        $this->dataNascimento = $dataNascimento;
        $this->peso = $peso;
        $this->altura = $altura;
        $this->sexo = $sexo;
    }
    
    public function getNome():string{
        return $this->nome;
    }
    
    public function getDataNascimento():DateTimeInterface{
        return $this->dataNascimento;
    }
    
    public function getPeso():float{
        return $this->peso;
    }
    
    public function getAltura():float{
        return $this->altura;
    }
    
    public function getSexo():SexoEnum{
        return $this->sexo;
    }
    
    public function getIdadeAtual(){
        return $this->dataNascimento->diff(new DateTimeImmutable(date('Y-m-d')))->y;
    }

    public function validarDadosEntrada() {
        if (empty($this->nome) || $this->peso === null || $this->altura === null || $this->sexo->value === '0' || empty($this->dataNascimento)) {
            throw new ExemploException('Todos os campos são obrigatórios.', 1);
        }

        if ($this->peso <= 10 || $this->peso > 650) {
            throw new ExemploException('O peso deve ser um número positivo e válido (10kg - 650kg).', 2);
        }

        if ($this->altura <= 0.5 || $this->altura > 2.8) {
            throw new ExemploException('A altura deve ser um número positivo e válido (0.5m - 2.8m).', 3);
        }

        if ($this->dataNascimento > new DateTimeImmutable()) {
            throw new ExemploException('Insira um ano válido.', 4);
        }
    }
}