<?php
// (09) Sessão disponível antes de qualquer operação.
session_start();

// (10) Funções carregadas uma única vez por execução.
require_once __DIR__ . '/includes/funcoes.php';

// (11) Só processa requisições POST; acesso direto volta ao formulário.
if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    header("Location: cadastro.php");
    exit;
}

// (12) Recebimento dos campos.
// Nome: trim (via limparTexto); campo ausente (null) ou inválido (false) vira "".
$nome = limparTexto(filter_input(INPUT_POST, "nome") ?? "");
// E-mail: FILTER_VALIDATE_EMAIL -> string válida | false (inválido) | null (ausente).
$email = filter_input(INPUT_POST, "email", FILTER_VALIDATE_EMAIL);
// Idade: FILTER_VALIDATE_INT -> int | false (inválido) | null (ausente).
$idade = filter_input(INPUT_POST, "idade", FILTER_VALIDATE_INT);

// (13) Validação, na ordem de prioridade definida pela instituição.
$erro = "";
if ($nome === "") {
    $erro = "O nome é obrigatório.";
} elseif ($email === false || $email === null) {
    $erro = "Informe um e-mail válido.";
} elseif ($idade === false || $idade === null || $idade <= 0) {
    $erro = "A idade deve ser um número inteiro maior que zero.";
}

// (14) Cadastro inválido: guarda erro e valores digitados e volta ao formulário.
if ($erro !== "") {
    // Valores brutos (o e-mail filtrado seria false/null se inválido).
    $emailDigitado = $_POST["email"] ?? "";
    $idadeDigitada = $_POST["idade"] ?? "";

    $_SESSION["erro"] = $erro;
    $_SESSION["dados_anteriores"] = [
        "nome"  => $nome,
        "email" => is_string($emailDigitado) ? $emailDigitado : "",
        "idade" => is_string($idadeDigitada) ? $idadeDigitada : "",
    ];
    header("Location: cadastro.php");
    exit;
}

// (15) Cadastro válido: guarda o aluno na sessão e segue para o resultado.
$_SESSION["aluno"] = [
    "nome"  => $nome,
    "email" => $email,
    "idade" => $idade,
];
$_SESSION["mensagem"] = "Cadastro realizado com sucesso!";
header("Location: resultado.php");
exit;
