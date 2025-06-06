<?php
header('Content-Type: application/json');
require 'conexao.php';

$busca = isset($_GET['busca']) ? trim($_GET['busca']) : "";

try {
  if($busca) {
    $stmt = $pdo->prepare("SELECT * FROM produtos WHERE nome LIKE :busca ORDER BY id DESC");
    $stmt->execute([':busca' => "%$busca%"]);
  } else {
    $stmt = $pdo->query("SELECT * FROM produtos ORDER BY id DESC");
  }

  $produtos = $stmt->fetchAll(PDO::FETCH_ASSOC);

  // Formata preço para exibição (ex: 1234.56 -> R$ 1.234,56)
  foreach($produtos as &$produto) {
    $produto['preco'] = "R$ " . number_format($produto['preco'], 2, ',', '.');
  }

  echo json_encode($produtos);

} catch(PDOException $e) {
  echo json_encode([]);
}
