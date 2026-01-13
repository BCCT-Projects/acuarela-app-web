<?php
    session_start();
    include "../includes/sdk.php";
    $a = new Acuarela();
    
    // Obtener versión específica si se proporciona, sino la activa
    $version = isset($_GET['version']) ? $_GET['version'] : null;
    
    if ($version) {
        $coppaNotice = $a->getCoppaNoticeByVersion($version);
    } else {
        $coppaNotice = $a->getCoppaNotice();
    }
    
    header('Content-Type: application/json');
    
    if (!$coppaNotice || !isset($coppaNotice->response) || empty($coppaNotice->response)) {
        http_response_code(404);
        echo json_encode([
            "success" => false,
            "error" => "Aviso COPPA no encontrado"
        ]);
        exit;
    }
    
    echo json_encode([
        "success" => true,
        "data" => $coppaNotice->response[0]
    ]);
?>
