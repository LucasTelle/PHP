<?php
declare(strict_types=1);

// ------------------------------------------------------------------
// CONFIGURAÇÃO DA CONEXÃO (PDO)
// ------------------------------------------------------------------
const DB_HOST = 'localhost';
const DB_NAME = 'loja';
const DB_USER = 'root';
const DB_PASS = '';

function conectar(): PDO
{
    $dsn = 'mysql:host=' . DB_HOST . ';dbname=' . DB_NAME . ';charset=utf8mb4';

    return new PDO($dsn, DB_USER, DB_PASS, [
        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES   => false,
    ]);
}

// Escapa texto para exibição segura em HTML
function e(string $valor): string
{
    return htmlspecialchars($valor, ENT_QUOTES, 'UTF-8');
}

// ------------------------------------------------------------------
// VARIÁVEIS INICIAIS
// ------------------------------------------------------------------
$nome       = '';
$categoria  = '';
$preco      = '';   // mantém o texto digitado para reexibir no formulário
$quantidade = '';
$erros      = [];
$sucesso    = '';

// ------------------------------------------------------------------
// PROCESSAMENTO: só entra aqui se o formulário foi enviado via POST
// ------------------------------------------------------------------
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // 1) RECEBER E LIMPAR (trim remove espaços desnecessários)
    $nome       = trim($_POST['nome']       ?? '');
    $categoria  = trim($_POST['categoria']  ?? '');
    $preco      = trim($_POST['preco']      ?? '');
    $quantidade = trim($_POST['quantidade'] ?? '');

    // Normaliza espaços repetidos no meio do texto ("Teclado   Mecânico")
    $nome      = preg_replace('/\s+/', ' ', $nome);
    $categoria = preg_replace('/\s+/', ' ', $categoria);

    // 2) VALIDAR
    if ($nome === '') {
        $erros[] = 'O nome do produto é obrigatório.';
    } elseif (mb_strlen($nome) > 100) {
        $erros[] = 'O nome deve ter no máximo 100 caracteres.';
    }

    if ($categoria === '') {
        $erros[] = 'A categoria é obrigatória.';
    } elseif (mb_strlen($categoria) > 50) {
        $erros[] = 'A categoria deve ter no máximo 50 caracteres.';
    }

    // Preço: aceita "250,00", "1.250,50" ou "250.00"
    $precoNumerico = null;
    if ($preco === '') {
        $erros[] = 'O preço é obrigatório.';
    } else {
        $precoTratado = $preco;

        if (str_contains($precoTratado, ',')) {
            // formato brasileiro: remove ponto de milhar e troca vírgula por ponto
            $precoTratado = str_replace('.', '', $precoTratado);
            $precoTratado = str_replace(',', '.', $precoTratado);
        }

        if (!is_numeric($precoTratado)) {
            $erros[] = 'O preço deve ser um valor numérico.';
        } elseif ((float) $precoTratado <= 0) {
            $erros[] = 'O preço deve ser maior que zero.';
        } else {
            $precoNumerico = round((float) $precoTratado, 2);
        }
    }

    // Quantidade: precisa ser inteiro e não negativo
    $quantidadeInt = null;
    if ($quantidade === '') {
        $erros[] = 'A quantidade em estoque é obrigatória.';
    } elseif (filter_var($quantidade, FILTER_VALIDATE_INT) === false) {
        $erros[] = 'A quantidade deve ser um número inteiro.';
    } elseif ((int) $quantidade < 0) {
        $erros[] = 'A quantidade em estoque não pode ser negativa.';
    } else {
        $quantidadeInt = (int) $quantidade;
    }

    // 3) DADOS VÁLIDOS? Só acessa o banco se NÃO houver erros
    if (empty($erros)) {
        try {
            $pdo = conectar();

            // PREPARE: SQL com parâmetros nomeados
            $sql = 'INSERT INTO produtos (nome, categoria, preco, quantidade)
                    VALUES (:nome, :categoria, :preco, :quantidade)';
            $stmt = $pdo->prepare($sql);

            // BIND
            $stmt->bindValue(':nome',       $nome,          PDO::PARAM_STR);
            $stmt->bindValue(':categoria',  $categoria,     PDO::PARAM_STR);
            $stmt->bindValue(':preco',      (string) $precoNumerico, PDO::PARAM_STR); // DECIMAL: string evita imprecisão de float
            $stmt->bindValue(':quantidade', $quantidadeInt, PDO::PARAM_INT);

            // EXECUTE
            $stmt->execute();

            $sucesso = 'Produto cadastrado com sucesso!';

            // Limpa o formulário após gravar
            $nome = $categoria = $preco = $quantidade = '';

        } catch (PDOException $ex) {
            // Em produção: registrar em log (error_log) e não expor detalhes
            $erros[] = 'Erro ao salvar o produto. Tente novamente mais tarde.';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Cadastro de Produtos</title>
    <style>
        body   { font-family: Arial, sans-serif; background:#f4f4f4; margin:0; padding:2rem; }
        .card  { max-width:460px; margin:auto; background:#fff; padding:1.5rem 2rem;
                 border-radius:8px; box-shadow:0 2px 8px rgba(0,0,0,.1); }
        h1     { margin-top:0; font-size:1.4rem; }
        label  { display:block; margin-top:1rem; font-weight:bold; }
        input  { width:100%; padding:.5rem; margin-top:.3rem; box-sizing:border-box; }
        button { margin-top:1.5rem; padding:.6rem 1.2rem; cursor:pointer; }
        .erro    { background:#fdecea; border-left:4px solid #d93025; padding:.75rem 1rem; margin-bottom:1rem; }
        .erro ul { margin:0; padding-left:1.2rem; }
        .sucesso { background:#e6f4ea; border-left:4px solid #1e8e3e; padding:.75rem 1rem; margin-bottom:1rem; }
    </style>
</head>
<body>
<div class="card">
    <h1>Cadastro de Produtos</h1>

    <?php if ($sucesso !== ''): ?>
        <div class="sucesso"><?= e($sucesso) ?></div>
    <?php endif; ?>

    <?php if (!empty($erros)): ?>
        <div class="erro">
            <strong>Corrija os seguintes dados:</strong>
            <ul>
                <?php foreach ($erros as $erro): ?>
                    <li><?= e($erro) ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>

    <form method="post" action="" novalidate>
        <label for="nome">Nome do produto</label>
        <input type="text" id="nome" name="nome" value="<?= e($nome) ?>">

        <label for="categoria">Categoria</label>
        <input type="text" id="categoria" name="categoria" value="<?= e($categoria) ?>">

        <label for="preco">Preço (R$)</label>
        <input type="text" id="preco" name="preco" inputmode="decimal"
               placeholder="250,00" value="<?= e($preco) ?>">

        <label for="quantidade">Quantidade em estoque</label>
        <input type="number" id="quantidade" name="quantidade" min="0" step="1"
               value="<?= e($quantidade) ?>">

        <button type="submit">Cadastrar</button>
    </form>
</div>
</body>
</html>