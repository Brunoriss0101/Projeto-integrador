<?php
session_start();
if (!isset($_SESSION['tentativas'])) {
    $_SESSION['tentativas'] = 0;
}

// CONFIGURAÇÃO DO BANCO
$host = "localhost";
$db_user = "root";
$db_pass = "";
$db_name = "seu_banco_de_dados";

// Estabelecendo conexão
$conn = mysqli_connect($host, $db_user, $db_pass, $db_name);

if (!$conn) {
    die("Falha na conexão: " . mysqli_connect_error());
}
?>
<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login de Usuário - Seguro</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background-color: #f8f9fa; }
        .login-container { max-width: 400px; margin-top: 100px; }
        .secure-badge { background-color: #198754; color: white; padding: 2px 5px; border-radius: 4px; font-size: 0.8rem; }
    </style>
</head>

<body>

    <div class="container d-flex justify-content-center">
        <div class="login-container w-100">
            <div class="card shadow border-0">
                <div class="card-body p-4">
                    <h2 class="text-center mb-4">Login <span class="secure-badge">SEGURO</span></h2>

                    <form method="POST">
                        <div class="mb-3">
                            <label class="form-label">Usuário:</label>
                            <input type="text" name="usuario" class="form-control" placeholder="Digite seu usuário" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Senha:</label>
                            <input type="password" name="senha" class="form-control" placeholder="Digite sua senha" required>
                        </div>

                        <div class="d-grid">
                            <button type="submit" class="btn btn-success" <?php echo ($_SESSION['tentativas'] >= 3) ? 'disabled' : ''; ?>>
                                Entrar Protegido
                            </button>
                        </div>
                    </form>

                    <div class="mt-3 text-center">
                        <?php
                        if ($_SERVER["REQUEST_METHOD"] == "POST" && $_SESSION['tentativas'] < 3) {
                            $usuario_input = $_POST['usuario'] ?? "";
                            $senha_input = $_POST['senha'] ?? "";

                            // --- PROTEÇÃO: PREPARED STATEMENTS ---
                            // 1. Preparamos a query com placeholders (?)
                            $stmt = mysqli_prepare($conn, "SELECT nome, senha, funcao FROM usuarios WHERE usuario = ?");
                            
                            // 2. Vinculamos o parâmetro (s = string)
                            mysqli_stmt_bind_param($stmt, "s", $usuario_input);
                            
                            // 3. Executamos de forma segura
                            mysqli_stmt_execute($stmt);
                            
                            $resultado = mysqli_stmt_get_result($stmt);

                            if ($user = mysqli_fetch_assoc($resultado)) {
                                // 4. Verificação de senha (Ideal usar password_verify se estiver usando hash)
                                if ($user['senha'] === $senha_input) {
                                    $nome = $user['nome'];
                                    $funcao = $user['funcao'];

                                    echo "<div class='alert alert-success mt-2'>";
                                    echo "Acesso autorizado! Bem-vindo, <strong>$nome</strong>.<br>";
                                    echo "Nível: <span class='badge bg-dark'>$funcao</span>";
                                    echo "</div>";
                                    
                                    $_SESSION['tentativas'] = 0;
                                    $_SESSION["nome"] = $nome;
                                    $_SESSION["funcao"] = $funcao;
                                } else {
                                    tentativaFalhou();
                                }
                            } else {
                                tentativaFalhou();
                            }
                            mysqli_stmt_close($stmt);
                        }

                        function tentativaFalhou() {
                            $_SESSION['tentativas'] += 1;
                            echo "<div class='alert alert-danger mt-2'>Acesso negado</div>";
                            if ($_SESSION['tentativas'] >= 3) {
                                echo "Você errou 3 vezes. Botão desativado!";
                            }
                        }
                        ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

</body>
</html>