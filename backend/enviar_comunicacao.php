<?php

require_once '../config/conexao.php';

$titulo = $_POST['titulo'] ?? '';
$descricao = $_POST['descricao'] ?? '';
$email = $_POST['email'] ?? '';
$unidade_id = $_POST['unidade_id'] ?? '';

if (empty($titulo) || empty($descricao) || empty($email) || empty($unidade_id)) {
    header('Location: ../nova-comunicacao.html?error=' . urlencode('Todos os campos são obrigatórios'));
    exit;
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    header('Location: ../nova-comunicacao.html?error=' . urlencode('E-mail inválido'));
    exit;
}

try {
    $check = $pdo->prepare("SELECT id FROM unidade_conservacao WHERE id = ?");
    $check->execute([$unidade_id]);
    if (!$check->fetch()) {
        header('Location: ../nova-comunicacao.html?error=' . urlencode('Unidade não encontrada'));
        exit;
    }
    
    $sql = "INSERT INTO comunicacao (titulo, descricao, data_hora, email, status, unidade_id) 
            VALUES (:titulo, :descricao, NOW(), :email, 0, :unidade_id)";
    
    $stmt = $pdo->prepare($sql);
    $stmt->execute([
        ':titulo' => $titulo,
        ':descricao' => $descricao,
        ':email' => $email,
        ':unidade_id' => $unidade_id
    ]);
    
    header('Location: ../nova-comunicacao.html?success=1');
    exit;
    
} catch(PDOException $e) {
    header('Location: ../nova-comunicacao.html?error=' . urlencode('Erro ao salvar: ' . $e->getMessage()));
    exit;
}
?>