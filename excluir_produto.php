<?php
header('Content-Type: application/json');
$host = "localhost";
$dbname = "mlfashion";
$user = "root";
$pass = "";

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $user, $pass);
} catch (PDOException $e) {
    echo json_encode(['sucesso' => false, 'mensagem' => "Erro ao conectar ao banco: " . $e->getMessage()]);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);
    if (!$id) {
        echo json_encode(['sucesso' => false, 'mensagem' => 'ID inválido']);
        exit;
    }

    // Buscar imagem para excluir arquivo
    $stmt = $pdo->prepare("SELECT imagem FROM produtos WHERE id = :id");
    $stmt->bindValue(':id', $id, PDO::PARAM_INT);
    $stmt->execute();
    $produto = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$produto) {
        echo json_encode(['sucesso' => false, 'mensagem' => 'Produto não encontrado']);
        exit;
    }

    $imagemPath = 'uploads/' . $produto['imagem'];
    if (file_exists($imagemPath)) {
        unlink($imagemPath);
    }

    // Excluir do banco
    $stmt = $pdo->prepare("DELETE FROM produtos WHERE id = :id");
    $stmt->bindValue(':id', $id, PDO::PARAM_INT);

    if ($stmt->execute()) {
        echo json_encode(['sucesso' => true]);
    } else {
        echo json_encode(['sucesso' => false, 'mensagem' => 'Erro ao excluir do banco']);
    }
} else {
    echo json_encode(['sucesso' => false, 'mensagem' => 'Método não permitido']);
}
?>
