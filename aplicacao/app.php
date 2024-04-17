<?php

require_once __DIR__ . '/src/Usuario.php';
require_once __DIR__ . '/src/CalculadoraImc.php';
require_once __DIR__ . '/src/SexoEnum.php';
require_once __DIR__ . '/src/ClassificacaoImcEnum.php';
require_once __DIR__ . '/src/ExemploException.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $nome = $_POST['nome'];
        $peso = floatval($_POST['peso']);
        $altura = floatval($_POST['altura']);
        $sexo = $_POST['sexo'];
        $dataNasc = $_POST['dataNasc'];

        if ($sexo !== 'Masculino' && $sexo !== 'Feminino') {
            throw new ExemploException('Todos os campos são obrigatórios.', 1);;
        }

        $usuario = new Usuario(
            nome: $nome,
            peso: $peso,
            altura: $altura,
            sexo: SexoEnum::from($_POST['sexo']),
            dataNascimento: new DateTimeImmutable($dataNasc)
        );
        $usuario->validarDadosEntrada();

        $calculadora = new CalculadoraImc($usuario);
        $imc = $calculadora->calcular();

        $classificacao = $calculadora->classificarPorFaixaEtariaSexo();

        $template = file_get_contents(__DIR__ . '/src/templates/resultado.html');

        $template = str_replace(
            [
                '{{USUARIO}}',
                '{{PESO}}',
                '{{ALTURA}}',
                '{{IDADE}}',
                '{{SEXO}}',
                '{{ERRO}}',
                '{{ICM}}',
                '{{CLASSIFICACAO}}'
            ],
            [
                $usuario->getNome(),
                $usuario->getPeso(),
                $usuario->getAltura(),
                $usuario->getIdadeAtual(),
                $usuario->getSexo()->value,
                '',
                $imc,
                $classificacao
            ],
            $template
        );

        echo $template;
    } catch (ExemploException $e) {
        $template = file_get_contents(__DIR__ . '/src/templates/resultado.html');

        $template = str_replace(
            [
                '{{USUARIO}}',
                '{{PESO}}',
                '{{ALTURA}}',
                '{{IDADE}}',
                '{{SEXO}}',
                '{{ERRO}}',
                '{{ICM}}',
                '{{CLASSIFICACAO}}'
            ],
            [
                '',
                '',
                '',
                '',
                '',
                $e->getMessage(),
                '',
                ''
            ],
            $template
        );

        echo $template;
    }
}