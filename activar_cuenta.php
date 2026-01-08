<?php
/**
 * Página de activación de cuenta para asistentes
 * El asistente accede aquí desde el email de invitación para crear su contraseña
 */
require_once 'includes/env.php';
require_once 'includes/src/Mandrill.php';

// Obtener el ID del asistente de la URL
$asistenteId = $_GET['id'] ?? null;

if (!$asistenteId) {
    $error = "Link de activación inválido. Por favor contacta al administrador.";
}

// Obtener información del asistente desde la API
$asistente = null;
if ($asistenteId) {
    $domain = Env::get('ACUARELA_API_URL', 'https://acuarelacore.com/api/');
    $endpoint = $domain . "acuarelausers/$asistenteId";
    
    $curl = curl_init();
    curl_setopt_array($curl, [
        CURLOPT_URL => $endpoint,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_TIMEOUT => 30,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_CUSTOMREQUEST => 'GET',
        CURLOPT_HTTPHEADER => ['Content-Type: application/json'],
    ]);
    
    $response = curl_exec($curl);
    $httpCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
    curl_close($curl);
    
    if ($httpCode === 200) {
        $data = json_decode($response);
        $asistente = $data->response[0] ?? $data;
    }
    
    if (!$asistente) {
        $error = "No se encontró la cuenta. El link puede haber expirado o ser inválido.";
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Activa tu cuenta - Acuarela</title>
    <link rel="icon" type="image/png" href="img/favicon.png">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #f0feff 0%, #e8f8f9 100%);
            min-height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 20px;
        }
        
        .container {
            background: white;
            border-radius: 16px;
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.1);
            padding: 40px;
            width: 100%;
            max-width: 420px;
        }
        
        .logo {
            text-align: center;
            margin-bottom: 30px;
        }
        
        .logo img {
            width: 180px;
        }
        
        h1 {
            color: #0cb5c3;
            font-size: 1.5rem;
            text-align: center;
            margin-bottom: 10px;
        }
        
        .subtitle {
            color: #666;
            text-align: center;
            margin-bottom: 30px;
            font-size: 0.95rem;
        }
        
        .welcome-name {
            color: #0cb5c3;
            font-weight: 600;
        }
        
        .form-group {
            margin-bottom: 20px;
        }
        
        label {
            display: block;
            color: #333;
            font-weight: 500;
            margin-bottom: 8px;
            font-size: 0.9rem;
        }
        
        input[type="password"] {
            width: 100%;
            padding: 14px 16px;
            border: 2px solid #e0e0e0;
            border-radius: 10px;
            font-size: 1rem;
            transition: border-color 0.3s, box-shadow 0.3s;
        }
        
        input[type="password"]:focus {
            outline: none;
            border-color: #0cb5c3;
            box-shadow: 0 0 0 3px rgba(12, 181, 195, 0.1);
        }
        
        .password-requirements {
            background: #f8f9fa;
            border-radius: 8px;
            padding: 12px 16px;
            margin-bottom: 20px;
            font-size: 0.85rem;
        }
        
        .password-requirements p {
            color: #666;
            margin-bottom: 8px;
        }
        
        .password-requirements ul {
            color: #888;
            margin-left: 20px;
        }
        
        .password-requirements li {
            margin-bottom: 4px;
        }
        
        .requirement-met {
            color: #28a745 !important;
        }
        
        button {
            width: 100%;
            padding: 14px;
            background: #0cb5c3;
            color: white;
            border: none;
            border-radius: 10px;
            font-size: 1rem;
            font-weight: 600;
            cursor: pointer;
            transition: background 0.3s, transform 0.2s;
        }
        
        button:hover {
            background: #0a9aa6;
        }
        
        button:active {
            transform: scale(0.98);
        }
        
        button:disabled {
            background: #ccc;
            cursor: not-allowed;
        }
        
        .error-message {
            background: #fee;
            color: #c00;
            padding: 12px 16px;
            border-radius: 8px;
            margin-bottom: 20px;
            font-size: 0.9rem;
            text-align: center;
        }
        
        .field-error {
            color: #c00;
            font-size: 0.8rem;
            margin-top: 6px;
        }
        
        .success-container {
            text-align: center;
        }
        
        .success-icon {
            width: 80px;
            height: 80px;
            background: #d4edda;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 20px;
        }
        
        .success-icon svg {
            width: 40px;
            height: 40px;
            color: #28a745;
        }
        
        .success-message {
            color: #155724;
            margin-bottom: 20px;
        }
        
        .app-download {
            margin-top: 30px;
            text-align: center;
        }
        
        .app-download p {
            color: #666;
            font-size: 0.9rem;
            margin-bottom: 10px;
        }
        
        .hidden {
            display: none;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="logo">
            <img src="https://bilingualchildcaretraining.com/design-system-acuarela/img/logos/logotipo_color.svg" alt="Acuarela">
        </div>
        
        <?php if (isset($error)): ?>
            <div class="error-message"><?= htmlspecialchars($error) ?></div>
        <?php elseif ($asistente): ?>
            <div id="formContainer">
                <h1>¡Bienvenido a Acuarela!</h1>
                <p class="subtitle">
                    Hola <span class="welcome-name"><?= htmlspecialchars($asistente->name ?? '') ?></span>, 
                    crea tu contraseña para activar tu cuenta.
                </p>
                
                <form id="activarForm" method="POST">
                    <input type="hidden" name="asistenteId" value="<?= htmlspecialchars($asistenteId) ?>">
                    
                    <div class="form-group">
                        <label for="password">Nueva contraseña</label>
                        <input type="password" id="password" name="password" placeholder="Ingresa tu contraseña" required>
                        <div class="field-error hidden" id="passwordError"></div>
                    </div>
                    
                    <div class="form-group">
                        <label for="confirmPassword">Confirmar contraseña</label>
                        <input type="password" id="confirmPassword" name="confirmPassword" placeholder="Repite tu contraseña" required>
                        <div class="field-error hidden" id="confirmError"></div>
                    </div>
                    
                    <div class="password-requirements">
                        <p>La contraseña debe tener:</p>
                        <ul>
                            <li id="req-length">Mínimo 6 caracteres</li>
                            <li id="req-upper">Al menos una mayúscula</li>
                            <li id="req-number">Al menos un número</li>
                            <li id="req-special">Al menos un carácter especial (!@#$%^&*_-.)</li>
                        </ul>
                    </div>
                    
                    <button type="submit" id="submitBtn">Activar mi cuenta</button>
                </form>
            </div>
            
            <div id="successContainer" class="success-container hidden">
                <div class="success-icon">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                    </svg>
                </div>
                <h1>¡Cuenta activada!</h1>
                <p class="success-message">Tu contraseña ha sido creada exitosamente. Ya puedes iniciar sesión en la app de Acuarela.</p>
                
                <div class="app-download">
                    <p>Descarga la app e inicia sesión con tu email y nueva contraseña.</p>
                </div>
            </div>
        <?php endif; ?>
    </div>
    
    <script>
        const form = document.getElementById('activarForm');
        const passwordInput = document.getElementById('password');
        const confirmInput = document.getElementById('confirmPassword');
        const submitBtn = document.getElementById('submitBtn');
        
        // Validación de requisitos en tiempo real
        if (passwordInput) {
            passwordInput.addEventListener('input', function() {
                const password = this.value;
                
                // Verificar cada requisito
                document.getElementById('req-length').className = password.length >= 6 ? 'requirement-met' : '';
                document.getElementById('req-upper').className = /[A-Z]/.test(password) ? 'requirement-met' : '';
                document.getElementById('req-number').className = /[0-9]/.test(password) ? 'requirement-met' : '';
                document.getElementById('req-special').className = /[!@#\$%\^&\*_\-\.]/.test(password) ? 'requirement-met' : '';
            });
        }
        
        if (form) {
            form.addEventListener('submit', async function(e) {
                e.preventDefault();
                
                const password = passwordInput.value;
                const confirm = confirmInput.value;
                const asistenteId = document.querySelector('input[name="asistenteId"]').value;
                
                // Limpiar errores
                document.getElementById('passwordError').classList.add('hidden');
                document.getElementById('confirmError').classList.add('hidden');
                
                // Validar contraseña
                const passwordRegex = /^(?=.*[A-Z])(?=.*[0-9])(?=.*[!@#\$%\^&\*_\-\.]).{6,}$/;
                if (!passwordRegex.test(password)) {
                    document.getElementById('passwordError').textContent = 'La contraseña no cumple con los requisitos';
                    document.getElementById('passwordError').classList.remove('hidden');
                    return;
                }
                
                // Validar confirmación
                if (password !== confirm) {
                    document.getElementById('confirmError').textContent = 'Las contraseñas no coinciden';
                    document.getElementById('confirmError').classList.remove('hidden');
                    return;
                }
                
                // Deshabilitar botón
                submitBtn.disabled = true;
                submitBtn.textContent = 'Activando...';
                
                try {
                    const response = await fetch('set/activarAsistente.php', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json'
                        },
                        body: JSON.stringify({
                            asistenteId: asistenteId,
                            password: password
                        })
                    });
                    
                    const data = await response.json();
                    
                    if (data.ok) {
                        document.getElementById('formContainer').classList.add('hidden');
                        document.getElementById('successContainer').classList.remove('hidden');
                    } else {
                        document.getElementById('passwordError').textContent = data.message || 'Error al activar la cuenta';
                        document.getElementById('passwordError').classList.remove('hidden');
                        submitBtn.disabled = false;
                        submitBtn.textContent = 'Activar mi cuenta';
                    }
                } catch (error) {
                    document.getElementById('passwordError').textContent = 'Error de conexión. Intenta nuevamente.';
                    document.getElementById('passwordError').classList.remove('hidden');
                    submitBtn.disabled = false;
                    submitBtn.textContent = 'Activar mi cuenta';
                }
            });
        }
    </script>
</body>
</html>

