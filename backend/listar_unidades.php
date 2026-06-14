<?php

header('Content-Type: application/json');
require_once '../config/conexao.php';

try {
    $sql = "
        SELECT 
            u.id,
            u.nome,
            u.descricao,
            u.data_criacao,
            u.imagem,
            i.nome as instituicao_nome,
            i.id as instituicao_id,
            GROUP_CONCAT(DISTINCT m.nome ORDER BY m.nome SEPARATOR ', ') as municipios
        FROM unidade_conservacao u
        LEFT JOIN instituicao i ON u.instituicao_id = i.id
        LEFT JOIN unidade_municipio um ON u.id = um.unidade_id
        LEFT JOIN municipio m ON um.municipio_id = m.id
        GROUP BY u.id
        ORDER BY u.nome
    ";
    
    $stmt = $pdo->query($sql);
    $unidades = $stmt->fetchAll();
    
    echo json_encode([
        'success' => true,
        'unidades' => $unidades
    ]);
    
} catch(PDOException $e) {
    echo json_encode([
        'success' => false,
        'error' => $e->getMessage()
    ]);
}
?>