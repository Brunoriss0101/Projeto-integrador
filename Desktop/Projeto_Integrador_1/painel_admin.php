<?php
session_start();
if (!isset($_SESSION['funcao']) || $_SESSION['funcao'] !== 'admin') {
    header("Location: login.php");
    exit;
}

// Conexão
$conn = mysqli_connect("localhost", "root", "", "aula09");

// Busca usuários para a gestão
$res_usuarios = mysqli_query($conn, "SELECT id, nome, usuario, funcao, status FROM usuarios");

// Busca logs para o relatório
$res_logs = mysqli_query($conn, "SELECT * FROM logs_acesso ORDER BY data_hora DESC LIMIT 10");
?>

<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <title>Console de Administração - Auditoria</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body class="bg-light">

    <nav class="navbar navbar-dark bg-dark mb-4">
        <div class="container">
            <span class="navbar-brand">🛡️ CyberDef Admin - <small>Logado como:
                    <?= $_SESSION['nome'] ?>
                </small></span>
            <a href="logout.php" class="btn btn-outline-danger btn-sm">Sair</a>
        </div>
    </nav>
    <?php
    // Lógica para Criar Usuário (Coloque no topo do painel_admin.php)
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['btn_criar'])) {
        $n_nome = $_POST['n_nome'];
        $n_user = $_POST['n_user'];
        $n_senha = $_POST['n_senha']; // Em produção, use password_hash()
        $n_funcao = $_POST['n_funcao'];

        $stmt_cad = mysqli_prepare($conn, "INSERT INTO usuarios (nome, usuario, senha, funcao, status) VALUES (?, ?, ?, ?, 1)");
        mysqli_stmt_bind_param($stmt_cad, "ssss", $n_nome, $n_user, $n_senha, $n_funcao);

        if (mysqli_stmt_execute($stmt_cad)) {
            echo "<div class='alert alert-success'>Usuário cadastrado com sucesso!</div>";
        }
        mysqli_stmt_close($stmt_cad);
        // Recarrega a página para atualizar a lista
        header("Refresh:1");
    }
    ?>

    <div class="card shadow-sm mb-4">
        <div class="card-header bg-success text-white"><strong>+ Novo Usuário</strong></div>
        <div class="card-body">
            <form method="POST" class="row g-2">
                <div class="col-md-3">
                    <input type="text" name="n_nome" class="form-control form-control-sm" placeholder="Nome Completo"
                        required>
                </div>
                <div class="col-md-3">
                    <input type="text" name="n_user" class="form-control form-control-sm" placeholder="Username"
                        required>
                </div>
                <div class="col-md-2">
                    <input type="password" name="n_senha" class="form-control form-control-sm" placeholder="Senha"
                        required>
                </div>
                <div class="col-md-2">
                    <select name="n_funcao" class="form-select form-select-sm">
                        <option value="usuario">Usuário</option>
                        <option value="admin">Admin</option>
                    </select>
                </div>
                <div class="col-md-2 d-grid">
                    <button type="submit" name="btn_criar" class="btn btn-sm btn-success">Cadastrar</button>
                </div>
            </form>
        </div>
    </div>

    <div class="container">
        <div class="row">
            <div class="col-md-7">
                <div class="card shadow-sm mb-4">
                    <div class="card-header bg-white"><strong>Gerenciar Contas</strong></div>
                    <div class="card-body">
                        <table class="table table-sm align-middle">
                            <thead>
                                <tr>
                                    <th>Nome</th>
                                    <th>Cargo</th>
                                    <th>Status</th>
                                    <th>Ações</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php while ($user = mysqli_fetch_assoc($res_usuarios)): ?>
                                    <tr>
                                        <td>
                                            <?= htmlspecialchars($user['nome']) ?>
                                        </td>
                                        <td><span class="badge bg-secondary">
                                                <?= $user['funcao'] ?>
                                            </span></td>
                                        <td>
                                            <?= $user['status'] ? '<span class="text-success">● Ativo</span>' : '<span class="text-danger">● Inativo</span>' ?>
                                        </td>
                                        <td>
                                            <div class="btn-group">
                                                <a href="acoes.php?act=toggle&id=<?= $user['id'] ?>"
                                                    class="btn btn-sm btn-outline-warning">Alternar</a>
                                                <a href="acoes.php?act=del&id=<?= $user['id'] ?>"
                                                    class="btn btn-sm btn-outline-danger"
                                                    onclick="return confirm('Excluir?')">Remover</a>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endwhile; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <div class="col-md-5">
                <div class="card shadow-sm border-0">
                    <div class="card-header bg-dark text-white"><strong>Logs de Acesso Recentes</strong></div>
                    <div class="card-body p-0">
                        <div class="list-group list-group-flush">
                            <?php while ($log = mysqli_fetch_assoc($res_logs)): ?>
                                <div class="list-group-item">
                                    <div class="d-flex w-100 justify-content-between">
                                        <h6 class="mb-1"><?= htmlspecialchars($log['usuario_texto']) ?></h6>
                                        <small><?= date('H:i:s', strtotime($log['data_hora'])) ?></small>
                                    </div>
                                    <small class="text-muted">IP: <?= $log['ip_origem'] ?></small>
                                    <span class="badge <?= $log['sucesso'] ? 'bg-success' : 'bg-danger' ?> float-end">
                                        <?= $log['sucesso'] ? 'Sucesso' : 'Falhou' ?>
                                    </span>
                                </div>
                            <?php endwhile; ?>
                        </div>
                    </div>
                </div>
            </div>

</html>