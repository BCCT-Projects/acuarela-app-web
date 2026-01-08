<?php
/**
 * Endpoint para activar cuenta de asistente (crear contraseña)
 * Recibe: asistenteId, password
 * Actualiza la contraseña del asistente en el API
 */
header('Content-Type: application/json');

require_once '../includes/env.php';

// Leer datos del body
$input = file_get_contents('php://input');
$data = json_decode($input, true);

$asistenteId = $data['asistenteId'] ?? null;
$password = $data['password'] ?? null;

// Validaciones
if (!$asistenteId || !$password) {
    echo json_encode([
        'ok' => false,
        'message' => 'Datos incompletos'
    ]);
    exit;
}

// Validar requisitos de contraseña
$passwordRegex = '/^(?=.*[A-Z])(?=.*[0-9])(?=.*[!@#\$%\^&\*_\-\.]).{6,}$/';
if (!preg_match($passwordRegex, $password)) {
    echo json_encode([
        'ok' => false,
        'message' => 'La contraseña no cumple con los requisitos de seguridad'
    ]);
    exit;
}

// Actualizar contraseña en el API
$domain = Env::get('ACUARELA_API_URL', 'https://acuarelacore.com/api/');
$endpoint = $domain . "acuarelausers/$asistenteId";

$updateData = [
    'pass' => $password,
    'password' => $password
];

$curl = curl_init();
curl_setopt_array($curl, [
    CURLOPT_URL => $endpoint,
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_TIMEOUT => 30,
    CURLOPT_FOLLOWLOCATION => true,
    CURLOPT_CUSTOMREQUEST => 'PUT',
    CURLOPT_POSTFIELDS => json_encode($updateData),
    CURLOPT_HTTPHEADER => ['Content-Type: application/json'],
]);

$response = curl_exec($curl);
$httpCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
$curlError = curl_error($curl);
curl_close($curl);

if ($curlError) {
    error_log("Error activando asistente $asistenteId: $curlError");
    echo json_encode([
        'ok' => false,
        'message' => 'Error de conexión. Intenta nuevamente.'
    ]);
    exit;
}

$responseData = json_decode($response, true);

if ($httpCode >= 200 && $httpCode < 300) {
    // Log de auditoría
    error_log("SEGURIDAD: Asistente $asistenteId activó su cuenta - " . date('Y-m-d H:i:s'));
    
    echo json_encode([
        'ok' => true,
        'message' => 'Cuenta activada exitosamente'
    ]);
} else {
    error_log("Error activando asistente $asistenteId: HTTP $httpCode - $response");
    echo json_encode([
        'ok' => false,
        'message' => 'Error al actualizar la contraseña'
    ]);
}

