<?php
session_start();
// Limpa as variáveis e destrói a sessão[cite: 1]
$_SESSION = array();
session_destroy();
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sessão Encerrada</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <script>
        // Redireciona automaticamente após 5 segundos
        setTimeout(function() {
            window.location.href = "login.php";
        }, 5000);
    </script>
    <style>
        body { background-color: #f8f9fa; }
        .logout-box { max-width: 450px; margin-top: 150px; }
    </style>
</head>
<body>
    <div class="container d-flex justify-content-center">
        <div class="logout-box text-center shadow p-5 bg-white rounded border">
            <div class="mb-4"><div class="display-1 text-success">&checkmark;</div></div>
            <h2 class="fw-bold">Você saiu!</h2>
            <p class="text-muted">Sua sessão foi encerrada com segurança.</p>
            <hr class="my-4">
            <div class="d-grid">
                <a href="login.php" class="btn btn-primary btn-lg">Voltar para o Login</a>
            </div>
            <p class="mt-3 small text-muted">Redirecionando em 5 segundos...</p>
        </div>
    </div>
</body>
</html>