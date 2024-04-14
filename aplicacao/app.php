<?php

require_once __DIR__ . '/src/Usuario.php';
require_once __DIR__ . '/src/CalculadoraImc.php';
require_once __DIR__ . '/src/SexoEnum.php';
require_once __DIR__ . '/src/ClassificacaoImcEnum.php';
require_once __DIR__ . '/src/ExemploException.php';

try {
    // Verifique se os campos do formulário foram enviados antes de criar o objeto Usuario
    if (isset($_POST['nome'], $_POST['peso'], $_POST['altura'], $_POST['sexo'], $_POST['data_nascimento'])) {
        $usuario = new Usuario(
            $_POST['nome'],
            new DateTimeImmutable($_POST['data_nascimento']),
            floatval($_POST['peso']),
            floatval($_POST['altura']),
            SexoEnum::from($_POST['sexo'])
        );

        // Validar dados de entrada
        $usuario->validarDadosEntrada();

        // Calcular IMC
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
    // Aqui você trata a exceção específica lançada pelo objeto Usuario
    echo "Erro: " . $e->getMessage();
} catch (Exception $e) {
    // Aqui você trata outras exceções possíveis, como campos do formulário não preenchidos corretamente
    echo "Erro: " . $e->getMessage();
}
