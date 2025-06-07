<?php
header('Content-Type: application/json');

$host = "localhost";
$user = "root";
$pass = "";
$dbname = "mlfashion";

$conn = new mysqli($host, $user, $pass, $dbname);
if ($conn->connect_error) {
    echo json_encode([]);
    exit;
}

$sql = "SELECT id, nome, preco, imagem FROM produtos ORDER BY id DESC";
$result = $conn->query($sql);

$produtos = [];
if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        $precoFormatado = 'R$ ' . number_format($row['preco'], 2, ',', '.');

        $produtos[] = [
            'id' => $row['id'],
            'nome' => $row['nome'],
            'preco' => $precoFormatado,
            'imagem' => $row['imagem']
        ];
    }
}
$conn->close();

echo json_encode($produtos);
?>
