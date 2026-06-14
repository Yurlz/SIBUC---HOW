<?php

header('Content-Type: application/json');
require_once '../config/conexao.php';

$unidade_id = isset($_GET['unidade_id']) ? (int)$_GET['unidade_id'] : null;

try {
    $sql = "
        SELECT 
            c.id,
            c.titulo,
            c.descricao,
            c.data_hora,
            c.email,
            c.status,
            u.nome as unidade_nome,
            u.id as unidade_id
        FROM comunicacao c
        LEFT JOIN unidade_conservacao u ON c.unidade_id = u.id
        WHERE 1=1
    ";
    
    $params = [];
    
    if ($unidade_id) {
        $sql .= " AND c.unidade_id = :unidade_id";
        $params[':unidade_id'] = $unidade_id;
    }
    
    $sql .= " ORDER BY c.data_hora DESC";
    
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    $comunicacoes = $stmt->fetchAll();
    
    foreach ($comunicacoes as &$c) {
        $c['status_texto'] = $c['status'] == 0 ? 'Em análise' : 'Analisada';
        $c['status_classe'] = $c['status'] == 0 ? 'dot-analise' : 'dot-analisada';
        $c['data_formatada'] = date('d/m/Y \à\s H:i', strtotime($c['data_hora']));
    }
    
    echo json_encode([
        'success' => true,
        'comunicacoes' => $comunicacoes
    ]);
    
} catch(PDOException $e) {
    echo json_encode([
        'success' => false,
        'error' => $e->getMessage()
    ]);
}
?>