<?php
header('Content-Type: application/json');

$host = "localhost";
$user = "root";      // ajuste aqui seu usuário MySQL
$pass = "TR7_samuka";          // ajuste aqui sua senha MySQL
$dbname = "mlfashion";

$conn = new mysqli($host, $user, $pass, $dbname);
if ($conn->connect_error) {
    echo json_encode(['success' => false, 'error' => 'Erro na conexão: ' . $conn->connect_error]);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nome = $_POST['nome'] ?? '';
    $preco = $_POST['preco'] ?? '';

    if (empty($nome) || empty($preco)) {
        echo json_encode(['success' => false, 'error' => 'Nome e preço são obrigatórios.']);
        exit;
    }

    if (isset($_FILES['imagemArquivo']) && $_FILES['imagemArquivo']['error'] === UPLOAD_ERR_OK) {
        $imagem = $_FILES['imagemArquivo'];
        $uploadDir = 'uploads/';
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }

        $ext = pathinfo($imagem['name'], PATHINFO_EXTENSION);
        $novoNome = uniqid('img_') . '.' . $ext;
        $uploadFile = $uploadDir . $novoNome;

        if (move_uploaded_file($imagem['tmp_name'], $uploadFile)) {
            $precoNumerico = str_replace(['R$', '.', ' '], '', $preco);
            $precoNumerico = str_replace(',', '.', $precoNumerico);

            $stmt = $conn->prepare("INSERT INTO produtos (nome, preco, imagem) VALUES (?, ?, ?)");
            if ($stmt === false) {
                echo json_encode(['success' => false, 'error' => 'Erro na query: ' . $conn->error]);
                exit;
            }
            $stmt->bind_param("sds", $nome, $precoNumerico, $uploadFile);
            $executou = $stmt->execute();

            if ($executou) {
                echo json_encode(['success' => true]);
            } else {
                echo json_encode(['success' => false, 'error' => 'Erro ao salvar no banco: ' . $stmt->error]);
            }
            $stmt->close();
        } else {
            echo json_encode(['success' => false, 'error' => 'Falha ao mover arquivo.']);
        }
    } else {
        echo json_encode(['success' => false, 'error' => 'Imagem não enviada ou inválida.']);
    }
} else {
    echo json_encode(['success' => false, 'error' => 'Método inválido.']);
}

$conn->close();
?>
