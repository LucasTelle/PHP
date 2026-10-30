<?php
// (04) Sessão iniciada antes de qualquer saída HTML.
session_start();

$nome = "";
$email = "";
$idade = "";
$mensagem = "";

// (05) Mensagem de erro (flash): lê e remove da sessão para não reaparecer.
if (isset($_SESSION["erro"])) {
    $mensagem = (string) $_SESSION["erro"];
    unset($_SESSION["erro"]);
}

// (06) Valores da tentativa anterior: lê, reaproveita e remove da sessão.
if (isset($_SESSION["dados_anteriores"]) && is_array($_SESSION["dados_anteriores"])) {
    $anteriores = $_SESSION["dados_anteriores"];
    $nome = (string) ($anteriores["nome"] ?? "");
    $email = (string) ($anteriores["email"] ?? "");
    $idade = (string) ($anteriores["idade"] ?? "");
    unset($_SESSION["dados_anteriores"]);
}

// (07) Cabeçalho compartilhado.
include __DIR__ . '/includes/cabecalho.php';
?>
<h2>Cadastro de aluno</h2>
<?php if ($mensagem !== ""): ?>
    <p><?= htmlspecialchars($mensagem, ENT_QUOTES, "UTF-8") ?></p>
<?php endif; ?>
<form method="POST" action="processar_cadastro.php">
    <label for="nome">Nome:</label>
    <input id="nome" name="nome" type="text" value="<?= htmlspecialchars($nome, ENT_QUOTES, 'UTF-8') ?>">
    <br><br>
    <label for="email">E-mail:</label>
    <input id="email" name="email" type="text" value="<?= htmlspecialchars($email, ENT_QUOTES, 'UTF-8') ?>">
    <br><br>
    <label for="idade">Idade:</label>
    <input id="idade" name="idade" type="number" value="<?= htmlspecialchars((string) $idade, ENT_QUOTES, 'UTF-8') ?>">
    <br><br>
    <button type="submit">Salvar cadastro</button>
</form>
<p><a href="index.php">Voltar</a></p>
<?php
// (08) Rodapé compartilhado.
include __DIR__ . '/includes/rodape.php';
?>
