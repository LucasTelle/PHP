<?php
// (21) Recupera a sessão atual.
session_start();

// (22) Remove todas as variáveis da sessão (aluno, mensagens, etc.).
session_unset();

// (23) Destrói a sessão no servidor.
session_destroy();

// (24) Volta para a página inicial e interrompe o script.
header("Location: index.php");
exit;
