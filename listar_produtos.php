<?php
header('Content-Type: application/json');
$host = "localhost";
$dbname = "mlfashion";
$user = "root";
$pass = "";

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $user, $pass);
} catch (PDOException $e) {
    echo json_encode([]);
    exit;
}

$stmt = $pdo->query("SELECT id, nome, preco, imagem FROM produtos ORDER BY id DESC");
$produtos = $stmt->fetchAll(PDO::FETCH_ASSOC);

foreach ($produtos as &$produto) {
    $produto['preco'] = "R$ " . number_format($produto['preco'], 2, ',', '.');
    $produto['imagem_url'] = 'uploads/' . $produto['imagem'];
}

echo json_encode($produtos);
?>
