<?php
session_start();

// Verifica se o usuário está logado e se é admin
// Se não estiver logado, redireciona para a tela de login padrão
if (!isset($_SESSION['funcao']) || $_SESSION['funcao'] !== 'admin') {
    header("Location: login.php"); 
    exit;
}

// Lógica do contador de acessos do painel[cite: 2]
if (!isset($_SESSION['contador'])) {
    $_SESSION['contador'] = 0;
}
$_SESSION['contador']++;
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Painel Administrativo</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background-color: #f4f7f6; }
        .navbar { margin-bottom: 30px; }
        .card { border-radius: 10px; }
    </style>
</head>
<body>

    <nav class="navbar navbar-dark bg-dark shadow">
        <div class="container">
            <a class="navbar-brand" href="#"><strong>Admin</strong>Panel</a>
            <div class="d-flex">
                <span class="navbar-text me-3 text-white">
                    Olá, <strong><?= htmlspecialchars($_SESSION['nome']); ?></strong>
                </span>
                <a href="logout.php" class="btn btn-outline-danger btn-sm">Sair</a>
            </div>
        </div>
    </nav>

    <div class="container">
        <div class="row">
            <div class="col-md-8 mx-auto">
                <div class="card shadow-sm">
                    <div class="card-header bg-white"><h5 class="mb-0">Informações do Perfil</h5></div>
                    <div class="card-body">
                        <div class="row mb-3">
                            <div class="col-sm-4 text-muted">Nome do Usuário:</div>
                            <div class="col-sm-8 text-dark fw-bold"><?= htmlspecialchars($_SESSION['nome']); ?></div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-sm-4 text-muted">Nível de Acesso:</div>
                            <div class="col-sm-8"><span class="badge bg-primary"><?= htmlspecialchars($_SESSION['funcao']); ?></span></div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-sm-4 text-muted">Acessos nesta sessão:</div>
                            <div class="col-sm-8"><span class="badge bg-info"><?= $_SESSION['contador']; ?></span></div>
                        </div>
                        <hr>
                        <p class="text-muted small">Acesso concedido via validação de banco de dados.</p>
                        <div class="mt-4">
                            <button class="btn btn-primary me-2">Gerenciar Usuários</button>
                            <button class="btn btn-secondary">Relatórios</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>