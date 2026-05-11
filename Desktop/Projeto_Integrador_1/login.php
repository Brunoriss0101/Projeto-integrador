<?php
session_start();
if (!isset($_SESSION['tentativas'])) {
    $_SESSION['tentativas'] = 0;
}

// CONFIGURAÇÃO DO BANCO
$host = "localhost";
$db_user = "root";
$db_pass = "";
$db_name = "aula09";

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
        body {
            background-color: #f8f9fa;
        }

        .login-container {
            max-width: 400px;
            margin-top: 100px;
        }

        .secure-badge {
            background-color: #198754;
            color: white;
            padding: 2px 5px;
            border-radius: 4px;
            font-size: 0.8rem;
        }
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
                            <input type="text" name="usuario" class="form-control" placeholder="Digite seu usuário"
                                required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Senha:</label>
                            <input type="password" name="senha" class="form-control" placeholder="Digite sua senha"
                                required>
                        </div>

                        <div class="d-grid">
                            <button type="submit" class="btn btn-success" <?php echo ($_SESSION['tentativas'] >= 3) ? 'disabled' : ''; ?>>
                                Entrar
                            </button>
                        </div>
                    </form>

                    <div class="mt-3 text-center">
                        <?php
                        if ($_SERVER["REQUEST_METHOD"] == "POST" && $_SESSION['tentativas'] < 3) {
                            $usuario_input = $_POST['usuario'] ?? "";
                            $senha_input = $_POST['senha'] ?? "";

                            // 1. Preparamos a consulta do usuário
                            $stmt = mysqli_prepare($conn, "SELECT id, nome, senha, funcao FROM usuarios WHERE usuario = ?");
                            mysqli_stmt_bind_param($stmt, "s", $usuario_input);
                            mysqli_stmt_execute($stmt);
                            $resultado = mysqli_stmt_get_result($stmt);

                            // ... (após buscar o usuário no banco)
                            $user = mysqli_fetch_assoc($resultado);

                            // Define as variáveis para o log
                            $user_id = $user ? $user['id'] : null; // Se não achar o user, manda NULL
                            $sucesso_log = ($user && $user['senha'] === $senha_input) ? 1 : 0;
                            $ip = $_SERVER['REMOTE_ADDR'];

                            // Registro do LOG ajustado para a nova estrutura
// Usamos "is i s" (integer, string, integer, string)
                            $stmt_log = mysqli_prepare($conn, "INSERT INTO logs_acesso (usuario_id, usuario_texto, sucesso, ip_origem) VALUES (?, ?, ?, ?)");
                            mysqli_stmt_bind_param($stmt_log, "isis", $user_id, $usuario_input, $sucesso_log, $ip);
                            mysqli_stmt_execute($stmt_log);
                            mysqli_stmt_close($stmt_log);

                            // ... (segue o restante da verificação de login)
                        
                            // 4. Lógica de Autenticação na Tela
                            if ($sucesso_log) {
                                $nome = $user['nome'];
                                $funcao = $user['funcao'];

                                echo "<div class='alert alert-success mt-2'>";
                                echo "Acesso autorizado! Bem-vindo, <strong>$nome</strong>.<br>";
                                echo "Nível: <span class='badge bg-dark'>$funcao</span>";
                                echo "</div>";

                                $_SESSION['tentativas'] = 0;
                                $_SESSION["nome"] = $nome;
                                $_SESSION["funcao"] = $funcao;

                                if ($funcao === 'admin') {
                                    echo "<div class='d-grid mt-3'><a href='painel_admin.php' class='btn btn-primary'>Acessar Painel</a></div>";
                                }
                            } else {
                                tentativaFalhou();
                            }
                            mysqli_stmt_close($stmt);
                        }
                        ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

</body>

</html>