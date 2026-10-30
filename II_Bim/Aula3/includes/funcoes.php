<?php
// Funções auxiliares reutilizáveis do Portal do Aluno.

/**
 * (25) Valida o formato de um endereço de e-mail.
 * filter_var() devolve a string (se válido) ou false (se inválido);
 * a comparação estrita converte o resultado para um booleano real.
 */
function validarEmail($email)
{
    return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
}

/**
 * (26 - bônus) Remove espaços desnecessários no início e no fim do texto.
 * O cast para string evita erro caso receba null ou false.
 */
function limparTexto($texto)
{
    return trim((string) $texto);
}
