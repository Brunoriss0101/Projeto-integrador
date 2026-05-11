<?php
session_start();

// 1. Defesa Cibernética: Proteção de acesso
if (!isset($_SESSION['funcao']) || $_SESSION['funcao'] !== 'admin') {
    exit("Acesso negado");
}

$conn = mysqli_connect("localhost", "root", "", "aula09");

// 2. Captura de parâmetros
$action = $_GET['act'] ?? '';
$id = intval($_GET['id'] ?? 0);

if ($id > 0) {
    if ($action === 'del') {
        // 3. Remoção Segura: Impede que o admin delete a si próprio comparando com a sessão 'nome'
        $stmt = mysqli_prepare($conn, "DELETE FROM usuarios WHERE id = ? AND nome != ?");
        mysqli_stmt_bind_param($stmt, "is", $id, $_SESSION['nome']);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);

    } elseif ($action === 'toggle') {
        // 4. Alternar Status (Ativo/Inativo)
        $stmt = mysqli_prepare($conn, "UPDATE usuarios SET status = NOT status WHERE id = ?");
        mysqli_stmt_bind_param($stmt, "i", $id);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);
    }
}

// 5. Redirecionamento único ao final do processo
mysqli_close($conn);
header("Location: painel_admin.php");
exit;
?>