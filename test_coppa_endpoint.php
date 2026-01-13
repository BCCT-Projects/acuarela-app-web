<?php
/**
 * Script de prueba para verificar el endpoint correcto de COPPA
 * Ejecutar desde: /miembros/acuarela-app-web/test_coppa_endpoint.php
 */

require_once __DIR__ . '/includes/env.php';

$domain = Env::get('ACUARELA_API_URL', 'https://acuarelacore.com/api/');

// Endpoints a probar
$endpoints = [
    'coppa-notice (singular)' => $domain . 'coppa-notice?publicationState=live&_limit=1',
    'coppa-notices (plural)' => $domain . 'coppa-notices?publicationState=live&_limit=1',
    'coppa-notice sin publicationState' => $domain . 'coppa-notice?_limit=1',
    'coppa-notices sin publicationState' => $domain . 'coppa-notices?_limit=1',
];

echo "<!DOCTYPE html><html><head><meta charset='UTF-8'><title>Test COPPA Endpoint</title>";
echo "<style>body{font-family:monospace;padding:20px;} .success{color:green;} .error{color:red;} pre{background:#f5f5f5;padding:10px;border:1px solid #ddd;overflow:auto;}</style></head><body>";
echo "<h1>Prueba de Endpoints COPPA</h1>";
echo "<p>Dominio API: <strong>$domain</strong></p><hr>";

foreach ($endpoints as $name => $endpoint) {
    echo "<h2>$name</h2>";
    echo "<p><strong>URL:</strong> <code>$endpoint</code></p>";
    
    $curl = curl_init();
    curl_setopt_array($curl, array(
        CURLOPT_URL => $endpoint,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => '',
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 10,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => 'GET',
        CURLOPT_HTTPHEADER => array('Content-Type: application/json'),
    ));
    
    $response = curl_exec($curl);
    $httpCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
    $error = curl_error($curl);
    curl_close($curl);
    
    echo "<p><strong>HTTP Code:</strong> ";
    if ($httpCode === 200) {
        echo "<span class='success'>$httpCode (OK)</span>";
    } else {
        echo "<span class='error'>$httpCode</span>";
    }
    echo "</p>";
    
    if ($error) {
        echo "<p class='error'><strong>Error cURL:</strong> $error</p>";
    }
    
    if ($httpCode === 200) {
        $data = json_decode($response, true);
        echo "<p class='success'><strong>✓ RESPUESTA VÁLIDA</strong></p>";
        echo "<pre>";
        echo "Estructura de respuesta:\n";
        print_r($data);
        echo "</pre>";
        
        // Verificar si tiene datos
        if (isset($data['data']) && is_array($data['data']) && !empty($data['data'])) {
            echo "<p class='success'><strong>✓ Datos encontrados: " . count($data['data']) . " registro(s)</strong></p>";
            echo "<pre>";
            echo "Primer registro:\n";
            print_r($data['data'][0]);
            echo "</pre>";
        } elseif (isset($data['response']) && is_array($data['response']) && !empty($data['response'])) {
            echo "<p class='success'><strong>✓ Datos encontrados: " . count($data['response']) . " registro(s)</strong></p>";
            echo "<pre>";
            echo "Primer registro:\n";
            print_r($data['response'][0]);
            echo "</pre>";
        } else {
            echo "<p class='error'><strong>✗ No se encontraron datos en la respuesta</strong></p>";
        }
    } else {
        echo "<p class='error'><strong>✗ Error en la petición</strong></p>";
        if ($response) {
            echo "<pre>Respuesta: " . htmlspecialchars(substr($response, 0, 500)) . "</pre>";
        }
    }
    
    echo "<hr>";
}

echo "<h2>Recomendación</h2>";
echo "<p>Usa el endpoint que devuelva <strong>HTTP 200</strong> y tenga <strong>datos</strong> en la respuesta.</p>";
echo "</body></html>";
?>
