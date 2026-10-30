# TESTES — Portal do Aluno

**Ambiente:** PHP 8.3.6 CLI, servidor embutido (`php -S 127.0.0.1:<porta>` dentro de `portal_aluno/`).
**Método:** requisições HTTP com `curl` mantendo um cookie de sessão (equivale a um navegador), seguindo os redirecionamentos quando indicado.
**Verificação de sintaxe:** `php -l` em todos os arquivos — sem erros. Nenhum warning/notice/deprecated no log do servidor.

| Nº | Cenário | Entrada | Resultado esperado | Resultado obtido | Status |
| --- | --- | --- | --- | --- | --- |
| 1 | Primeiro acesso | Abrir `index.php` | Título, links e rodapé visíveis | Título "Portal do Aluno", links Cadastrar/Consultar/Sair e `<footer>` + `</html>` presentes | ✅ |
| 2 | Nome não informado | Nome vazio, `ana@senac.br`, `22` | Erro de nome obrigatório | "O nome é obrigatório."; e-mail e idade preservados | ✅ |
| 3 | E-mail inválido | `Ana`, `teste`, `22` | Erro de e-mail inválido | "Informe um e-mail válido."; campos preenchidos mantidos | ✅ |
| 4 | Idade inválida | `Ana`, `ana@senac.br`, `0` | Erro de idade | "A idade deve ser um número inteiro maior que zero." | ✅ |
| 5 | Cadastro válido | `Ana Souza`, `ana@senac.br`, `22` | Redireciona (302) ao resultado com os dados | `302` para `resultado.php`; exibe mensagem de sucesso, nome, e-mail e idade | ✅ |
| 6 | Atualizar o resultado | F5 após o cadastro | Dados ficam; mensagem some | 1º acesso: mensagem + dados. 2º e 3º acessos: só os dados | ✅ |
| 7 | Encerrar sessão | `logout.php`, depois `resultado.php` | Sem cadastro ativo | `302` para `index.php`; resultado mostra "Nenhum aluno cadastrado nesta sessão." | ✅ |
| 8 | HTML no nome | `<h1>Ana</h1>` | Texto literal, sem título HTML | Saída `Nome: &lt;h1&gt;Ana&lt;/h1&gt;`; nenhum `<h1>Ana` no HTML | ✅ |
| 9 | Preservação de dados | `Mariana Oliveira`, `mariana.com`, `21` | Volta ao formulário com valores | Mensagem de e-mail inválido e os três campos preenchidos | ✅ |

## Testes adicionais

| Ação | Resultado |
| --- | --- |
| `GET` direto em `processar_cadastro.php` | `302` para `cadastro.php`, sem processar |
| `POST` sem os campos (`x=1`) | `302` para `cadastro.php` com "O nome é obrigatório." (sem warnings) |
| Campos enviados como array (`nome[]=a&email[]=b&idade[]=1`) | `200`, sem erro fatal |
| Idade `abc` e `1.5` | Rejeitadas com a mensagem de idade |
| E-mail com aspas (`a"b@x`) devolvido ao formulário | Escapado como `a&quot;b@x`, sem quebrar o atributo `value` |
| Erro exibido uma vez | Ao recarregar `cadastro.php`, mensagem e valores somem da sessão |

## Observação
Os testes foram feitos por linha de comando (curl), não em navegador. Recomenda-se repetir o fluxo uma vez no navegador (XAMPP: `http://localhost/portal_aluno/index.php`).

## Perguntas do desafio final (respostas curtas)

1. **Por que executar certas operações antes do HTML?** `session_start()` e `header()` enviam cabeçalhos HTTP, que precisam ser transmitidos antes do corpo da resposta. Depois que qualquer HTML (ou `echo`) sai, dá o erro "headers already sent".
2. **Vantagem dos componentes compartilhados?** Evitam duplicação: alterar o cabeçalho ou o rodapé em um arquivo muda todas as páginas, mantendo padrão visual e facilitando a manutenção.
3. **Por que os dados continuam após um redirecionamento?** O HTTP não guarda estado; os dados ficam no servidor, em `$_SESSION`, ligados ao ID da sessão enviado por cookie em cada requisição.
4. **Como a mensagem de sucesso aparece só uma vez?** É uma *flash message*: `resultado.php` lê `$_SESSION["mensagem"] ?? ""` e executa `unset` em seguida, sem tocar em `$_SESSION["aluno"]`.
5. **`unset` de uma chave x encerrar a sessão?** `unset($_SESSION["mensagem"])` remove só aquele item e a sessão segue ativa. `session_unset()` + `session_destroy()` apagam todas as variáveis e destroem a sessão no servidor.
6. **Por que não confiar nos dados do formulário?** O cliente controla o que envia: pode mandar vazio, tipos errados ou HTML/script malicioso. Por isso validamos no servidor e escapamos na exibição com `htmlspecialchars()` (evita XSS).
