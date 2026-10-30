<?php
// (16) Sessão iniciada antes de qualquer HTML.
session_start();

// (17) Aluno cadastrado (ou null, se não houver).
$aluno = $_SESSION["aluno"] ?? null;

// (18) Mensagem de sucesso (flash): lida e removida; o aluno permanece.
$mensagem = (string) ($_SESSION["mensagem"] ?? "");
unset($_SESSION["mensagem"]);

// (19) Cabeçalho compartilhado.
include __DIR__ . '/includes/cabecalho.php';
?>
<h2>Resultado do cadastro</h2>
<?php if ($mensagem !== ""): ?>
    <p><?= htmlspecialchars($mensagem, ENT_QUOTES, "UTF-8") ?></p>
<?php endif; ?>
<?php if ($aluno !== null): ?>
    <p>Nome: <?= htmlspecialchars($aluno["nome"], ENT_QUOTES, "UTF-8") ?></p>
    <p>E-mail: <?= htmlspecialchars($aluno["email"], ENT_QUOTES, "UTF-8") ?></p>
    <p>Idade: <?= (int) $aluno["idade"] ?></p>
<?php else: ?>
    <p>Nenhum aluno cadastrado nesta sessão.</p>
<?php endif; ?>
<p><a href="cadastro.php">Novo cadastro</a> | <a href="logout.php">Encerrar sessão</a></p>
<?php
// (20) Rodapé compartilhado.
include __DIR__ . '/includes/rodape.php';
?>
