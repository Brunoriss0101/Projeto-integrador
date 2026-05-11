<?php
session_start();
if (!isset($_SESSION['tentativas'])) {
    $_SESSION['tentativas'] = 0;
}

// CONFIGURAÇÃO DO BANCO ADAPTADA
$host = "localhost";
$db_user = "root";
$db_pass = "";
$db_name = "aula09"; // Alterado para o banco correto

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
    <title>Login Vulnerável - Demonstração</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f8f9fa;
        }

        .login-container {
            max-width: 400px;
            margin-top: 100px;
        }

        .vuln-badge {
            background-color: #dc3545;
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
                    <h2 class="text-center mb-4">Login <span class="vuln-badge">VULNERÁVEL</span></h2>

                    <form method="POST">
                        <div class="mb-3">
                            <label class="form-label">Usuário:</label>
                            <input type="text" name="usuario" class="form-control" placeholder="Ex: ' OR '1'='1"
                                required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Senha:</label>
                            <input type="password" name="senha" class="form-control" placeholder="Senha qualquer"
                                required>
                        </div>

                        <div class="d-grid">
                            <button type="submit" class="btn btn-danger" <?php echo ($_SESSION['tentativas'] >= 3) ? 'disabled' : ''; ?>>
                                Entrar (Modo Inseguro)
                            </button>
                        </div>
                    </form>

                    <div class="mt-3 text-center">
                        <?php
                        if ($_SERVER["REQUEST_METHOD"] == "POST" && $_SESSION['tentativas'] < 3) {
                            $usuario = $_POST['usuario']; // Captura direta sem tratamento
                            $senha = $_POST['senha'];     // Captura direta sem tratamento
                        
                            // A VULNERABILIDADE: Concatenação direta que permite SQL Injection
                            // Ajustado para incluir a coluna 'senha' na verificação conforme o banco aula09
                            $sql = "SELECT nome, funcao FROM usuarios WHERE usuario = '$usuario' AND senha = '$senha'";

                            // Exibição da Query (Essencial para sua explicação técnica)
                            echo "<div class='alert alert-warning p-2' style='font-size: 0.75rem;'>
                                <strong>Query executada:</strong><br><code>$sql</code>
                              </div>";

                            $result = mysqli_query($conn, $sql);

                            if ($result && mysqli_num_rows($result) > 0) {
                                $user_data = mysqli_fetch_assoc($result);
                                $nome = $user_data['nome'];
                                $funcao = $user_data['funcao'];

                                echo "<div class='alert alert-success mt-2'>";
                                echo "Acesso autorizado! Bem-vindo, <strong>$nome</strong>.<br>";
                                echo "Nível: <span class='badge bg-dark'>$funcao</span>";
                                echo "</div>";

                                // Define sessões para que o login "funcione" mesmo sendo vulnerável
                                $_SESSION['tentativas'] = 0;
                                $_SESSION['nome'] = $nome;
                                $_SESSION['funcao'] = $funcao;
                            } else {
                                $_SESSION['tentativas'] += 1;
                                echo "<div class='alert alert-danger mt-2'>Acesso negado</div>";
                            }
                        }
                        if ($_SESSION['tentativas'] >= 3) {
                            echo "<div class='text-danger'>Bloqueado por excesso de tentativas!</div>";
                        }
                        ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

</body>

</html>