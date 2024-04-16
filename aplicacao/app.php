<?php

require_once __DIR__ . '/src/Usuario.php';
require_once __DIR__ . '/src/CalculadoraImc.php';
require_once __DIR__ . '/src/SexoEnum.php';
require_once __DIR__ . '/src/ClassificacaoImcEnum.php';
require_once __DIR__ . '/src/ExemploException.php';

try {
    if (isset($_POST['nome'], $_POST['peso'], $_POST['altura'], $_POST['sexo'], $_POST['data_nascimento'])) {
        $usuario = new Usuario(
            $_POST['nome'],
            new DateTimeImmutable($_POST['data_nascimento']),
            floatval($_POST['peso']),
            floatval($_POST['altura']),
            SexoEnum::from($_POST['sexo'])
        );

        $usuario->validarDadosEntrada();

        $imc = $usuario->calcularIMC();

        // 1) ler o template de resposta
        $template = file_get_contents(__DIR__ . '/src/templates/resultado.html');

        // 2) trocar cada valor estático pelo valor do script
        $template = str_replace(
            [
                '{{USUARIO}}',
                '{{PESO}}',
                '{{ALTURA}}',
                '{{IDADE}}',
                '{{SEXO}}',
                '{{ICM}}',
                '{{CLASSIFICACAO}}'
            ],
            [
                $usuario->getNome(),
                $usuario->getPeso(),
                $usuario->getAltura(),
                $usuario->getIdadeAtual(),
                $usuario->getSexo()->value,
                $imc,
                ClassificacaoImcEnum::classifica($imc)
            ],
            $template);

        echo $template;
    } else {
        throw new Exception("Por favor, preencha todos os campos do formulário.");
    }
} catch (ExemploException $e) {
    echo "Erro: " . $e->getMessage();
} catch (Exception $e) {
    echo "Erro: " . $e->getMessage();
}
