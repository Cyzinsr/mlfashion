<?php
$host = "localhost";
$db   = "mlfashion";
$user = "seu_usuario";      // ajuste aqui
$pass = "sua_senha";        // ajuste aqui

try {
  $pdo = new PDO("mysql:host=$host;dbname=$db;charset=utf8", $user, $pass);
  $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch(PDOException $e) {
  die(json_encode(['success' => false, 'message' => 'Erro na conexão: ' . $e->getMessage()]));
}
?>
