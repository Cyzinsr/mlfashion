<?php
header('Content-Type: application/json');
$host = "localhost";
$dbname = "mlfashion";
$user = "root";   // Ajuste seu usuário MySQL
$pass = "";       // Ajuste sua senha MySQL

// Conexão PDO
try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $user, $pass);
} catch (PDOException $e) {
    echo json_encode(['sucesso' => false, 'mensagem' => "Erro ao conectar ao banco de dados: " . $e->getMessage()]);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nome = filter_input(INPUT_POST, 'nome', FILTER_SANITIZE_STRING);
    $preco = filter_input(INPUT_POST, 'preco', FILTER_SANITIZE_STRING);
    $preco = str_replace(['R$', '.', ' '], '', $preco); // Remover R$, pontos e espaços
    $preco = str_replace(',', '.', $preco); // Trocar vírgula por ponto
    $preco = floatval($preco);

    if (!$nome || !$preco || !isset($_FILES['imagemArquivo'])) {
        echo json_encode(['sucesso' => false, 'mensagem' => 'Dados incompletos']);
        exit;
    }

    // Upload da imagem
    $extensoesPermitidas = ['jpg', 'jpeg', 'png', 'gif'];
    $arquivo = $_FILES['imagemArquivo'];
    $ext = strtolower(pathinfo($arquivo['name'], PATHINFO_EXTENSION));

    if (!in_array($ext, $extensoesPermitidas)) {
        echo json_encode(['sucesso' => false, 'mensagem' => 'Extensão de imagem não permitida']);
        exit;
    }

    $pastaUploads = 'uploads/';
    if (!is_dir($pastaUploads)) {
        mkdir($pastaUploads, 0755, true);
    }

    $nomeArquivo = uniqid() . "." . $ext;
    $caminhoCompleto = $pastaUploads . $nomeArquivo;

    if (!move_uploaded_file($arquivo['tmp_name'], $caminhoCompleto)) {
        echo json_encode(['sucesso' => false, 'mensagem' => 'Erro ao enviar a imagem']);
        exit;
    }

    // Inserir no banco
    $stmt = $pdo->prepare("INSERT INTO produtos (nome, preco, imagem) VALUES (:nome, :preco, :imagem)");
    $stmt->bindValue(':nome', $nome);
    $stmt->bindValue(':preco', $preco);
    $stmt->bindValue(':imagem', $nomeArquivo);

    if ($stmt->execute()) {
        echo json_encode(['sucesso' => true]);
    } else {
        echo json_encode(['sucesso' => false, 'mensagem' => 'Erro ao salvar no banco']);
    }
} else {
    echo json_encode(['sucesso' => false, 'mensagem' => 'Método não permitido']);
}
?>
