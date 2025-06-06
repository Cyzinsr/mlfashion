<?php
header('Content-Type: application/json');
require 'conexao.php';

// Verifica se recebeu dados
if(!isset($_POST['nome']) || !isset($_POST['preco']) || !isset($_FILES['imagemArquivo'])) {
  echo json_encode(['success' => false, 'message' => 'Dados incompletos']);
  exit;
}

$nome = trim($_POST['nome']);
$preco = trim($_POST['preco']); // Ex: "R$ 1.234,56"

// Convertendo preço para formato numérico (decimal ponto)
$preco = str_replace(['R$', '.', ' '], '', $preco);
$preco = str_replace(',', '.', $preco);
$preco = floatval($preco);

// Tratando upload da imagem
if($_FILES['imagemArquivo']['error'] !== UPLOAD_ERR_OK) {
  echo json_encode(['success' => false, 'message' => 'Erro no upload da imagem']);
  exit;
}

$nomeArquivo = uniqid() . '_' . basename($_FILES['imagemArquivo']['name']);
$caminhoDestino = 'imagens/' . $nomeArquivo;

// Cria pasta imagens caso não exista
if(!is_dir('imagens')) {
  mkdir('imagens', 0755, true);
}

if(!move_uploaded_file($_FILES['imagemArquivo']['tmp_name'], $caminhoDestino)) {
  echo json_encode(['success' => false, 'message' => 'Falha ao salvar imagem']);
  exit;
}

// Insere no banco
try {
  $sql = "INSERT INTO produtos (nome, preco, imagem_url) VALUES (:nome, :preco, :imagem_url)";
  $stmt = $pdo->prepare($sql);
  $stmt->execute([
    ':nome' => $nome,
    ':preco' => $preco,
    ':imagem_url' => $caminhoDestino
  ]);

  echo json_encode(['success' => true]);
} catch(PDOException $e) {
  echo json_encode(['success' => false, 'message' => 'Erro no banco: ' . $e->getMessage()]);
}
