<?php
// (01) Sessão iniciada antes de qualquer HTML.
session_start();

// (02) Cabeçalho compartilhado.
include __DIR__ . '/includes/cabecalho.php';
?>
<h2>Portal do Aluno</h2>
<p>Bem-vindo ao sistema de cadastro do Senac!</p>
<p><a href="cadastro.php">Cadastrar aluno</a></p>
<p><a href="resultado.php">Consultar meu cadastro</a></p>
<p><a href="logout.php">Sair</a></p>
<?php
// (03) Rodapé compartilhado.
include __DIR__ . '/includes/rodape.php';
?>
