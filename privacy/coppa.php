<?php
    // Página pública del Aviso COPPA - No requiere autenticación
    require_once __DIR__ . '/../includes/env.php';
    
    // Función para obtener aviso COPPA sin autenticación
    function getPublicCoppaNotice() {
        $domain = Env::get('ACUARELA_API_URL', 'https://acuarelacore.com/api/');
        // Buscar cualquier aviso publicado (sin filtrar por status, ya que puede variar)
        $endpoint = $domain . 'coppa-notices?_sort=notice_published_date:DESC&_limit=1&publicationState=live';
        
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
            CURLOPT_HTTPHEADER => array(
                'Content-Type: application/json'
            ),
        ));
        
        $response = curl_exec($curl);
        $httpCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        $error = curl_error($curl);
        curl_close($curl);
        
        if ($httpCode !== 200) {
            error_log("COPPA Notice API Error: HTTP $httpCode - $error");
            return null;
        }
        
        $data = json_decode($response, true);
        
        // Strapi puede devolver datos en diferentes formatos
        // Intentar diferentes estructuras de respuesta
        if (isset($data['data']) && is_array($data['data']) && !empty($data['data'])) {
            return (object)['response' => array_map(function($item) { return (object)$item; }, $data['data'])];
        }
        
        if (isset($data['response']) && is_array($data['response']) && !empty($data['response'])) {
            return (object)['response' => array_map(function($item) { return (object)$item; }, $data['response'])];
        }
        
        if (is_array($data) && !empty($data)) {
            return (object)['response' => array_map(function($item) { return (object)$item; }, $data)];
        }
        
        return null;
    }
    
    // Obtener versión activa del aviso COPPA
    $coppaNotice = getPublicCoppaNotice();
    
    // Si no hay versión activa, mostrar mensaje con debug
    if (!$coppaNotice || !isset($coppaNotice->response) || empty($coppaNotice->response)) {
        http_response_code(404);
        // Modo debug temporal - agregar ?debug=1 a la URL para ver detalles
        $debug = isset($_GET['debug']) && $_GET['debug'] === '1';
        if ($debug) {
            $domain = Env::get('ACUARELA_API_URL', 'https://acuarelacore.com/api/');
            $testEndpoint = $domain . 'coppa-notices?_sort=notice_published_date:DESC&_limit=1&publicationState=live';
            
            $curl = curl_init();
            curl_setopt_array($curl, array(
                CURLOPT_URL => $testEndpoint,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => '',
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 10,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => 'GET',
                CURLOPT_HTTPHEADER => array('Content-Type: application/json'),
            ));
            
            $testResponse = curl_exec($curl);
            $testHttpCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
            curl_close($curl);
            
            echo '<pre style="background:#f5f5f5;padding:20px;border:1px solid #ddd;">';
            echo "<strong>DEBUG - Información de la API COPPA</strong>\n\n";
            echo "Endpoint: $testEndpoint\n";
            echo "HTTP Code: $testHttpCode\n\n";
            echo "Response completa:\n";
            print_r(json_decode($testResponse, true));
            echo "\n\n<strong>Verifica:</strong>\n";
            echo "1. Que los permisos públicos estén configurados en Strapi (find y findOne)\n";
            echo "2. Que el aviso esté publicado (no en borrador)\n";
            echo "3. Que la API de Strapi esté accesible\n";
            echo '</pre>';
        } else {
            die('Aviso COPPA no disponible. Por favor contacte al administrador. <a href="?debug=1">Ver detalles</a>');
        }
        exit;
    }
    
    $notice = $coppaNotice->response[0];
    $version = $notice->version ?? 'v1.0';
    $publishedAt = isset($notice->notice_published_date) ? date('d/m/Y', strtotime($notice->notice_published_date)) : date('d/m/Y');
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Aviso de Privacidad COPPA - Acuarela</title>
    <link rel="stylesheet" href="../css/acuarela_theme.css?v=<?= time() ?>">
    <link rel="stylesheet" href="../css/styles.css?v=<?= time() ?>">
    <link rel="stylesheet" href="../css/coppa.css?v=<?= time() ?>">
    <link rel="shortcut icon" href="../img/favicon.png">
</head>
<body class="coppa-page">
    <div class="coppa-container">
        <header class="coppa-header">
            <div class="coppa-header__content">
                <img src="../img/logos/logotipo_invertido.svg" alt="Acuarela Logo" class="coppa-header__logo">
                <h1 class="coppa-header__title">Aviso de Privacidad COPPA</h1>
                <p class="coppa-header__subtitle">Para Padres y Tutores</p>
            </div>
        </header>

        <main class="coppa-main">
            <div class="coppa-notice">
                <!-- Información de versión -->
                <div class="coppa-notice__meta">
                    <p class="coppa-notice__version">Versión: <strong><?= htmlspecialchars($version) ?></strong></p>
                    <p class="coppa-notice__date">Fecha de publicación: <strong><?= htmlspecialchars($publishedAt) ?></strong></p>
                    <?php if (isset($notice->checksum)): ?>
                        <p class="coppa-notice__checksum">ID de verificación: <code><?= substr($notice->checksum, 0, 16) ?>...</code></p>
                    <?php endif; ?>
                </div>

                <!-- Resumen ejecutivo -->
                <?php if (isset($notice->summary) && !empty($notice->summary)): ?>
                <section class="coppa-section coppa-section--summary">
                    <h2 class="coppa-section__title">Resumen Ejecutivo</h2>
                    <div class="coppa-section__content">
                        <?= nl2br(htmlspecialchars($notice->summary)) ?>
                    </div>
                </section>
                <?php endif; ?>

                <!-- Contenido completo -->
                <section class="coppa-section">
                    <h2 class="coppa-section__title">Aviso Completo de Privacidad COPPA</h2>
                    
                    <!-- 1. Identidad del Operador -->
                    <div class="coppa-subsection">
                        <h3 class="coppa-subsection__title">1. Identidad del Operador</h3>
                        <div class="coppa-subsection__content">
                            <?php if (isset($notice->operator_name)): ?>
                                <p><strong>Nombre Legal:</strong> <?= htmlspecialchars($notice->operator_name) ?></p>
                            <?php else: ?>
                                <p><strong>Nombre Legal:</strong> Bilingual Child Care Training (BCCT)</p>
                            <?php endif; ?>
                            
                            <?php if (isset($notice->operator_contact)): ?>
                                <div class="coppa-contact">
                                    <?= nl2br(htmlspecialchars($notice->operator_contact)) ?>
                                </div>
                            <?php else: ?>
                                <div class="coppa-contact">
                                    <p><strong>Información de Contacto:</strong></p>
                                    <p>Email: info@acuarela.app</p>
                                    <p>Para consultas sobre privacidad: privacy@acuarela.app</p>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>

                    <!-- 2. Datos Recopilados -->
                    <div class="coppa-subsection">
                        <h3 class="coppa-subsection__title">2. Datos Recopilados del Menor</h3>
                        <div class="coppa-subsection__content">
                            <?php if (isset($notice->data_collected)): ?>
                                <?= nl2br(htmlspecialchars($notice->data_collected)) ?>
                            <?php else: ?>
                                <p>Recopilamos la siguiente información de los menores:</p>
                                <ul>
                                    <li><strong>Información de identificación:</strong> Nombre completo, fecha de nacimiento, género</li>
                                    <li><strong>Información educativa:</strong> Actividades, asistencia, progreso académico, fotografías y videos de actividades</li>
                                    <li><strong>Información de salud:</strong> Alergias, condiciones médicas, medicamentos (cuando sea necesario para el cuidado)</li>
                                    <li><strong>Información de contacto:</strong> Datos de padres/tutores y contactos de emergencia</li>
                                </ul>
                            <?php endif; ?>
                        </div>
                    </div>

                    <!-- 3. Uso de la Información -->
                    <div class="coppa-subsection">
                        <h3 class="coppa-subsection__title">3. Uso de la Información</h3>
                        <div class="coppa-subsection__content">
                            <?php if (isset($notice->data_usage)): ?>
                                <?= nl2br(htmlspecialchars($notice->data_usage)) ?>
                            <?php else: ?>
                                <p>Utilizamos la información recopilada exclusivamente para:</p>
                                <ul>
                                    <li>Gestión educativa y pedagógica del menor</li>
                                    <li>Comunicación con padres y tutores sobre el progreso del menor</li>
                                    <li>Seguridad y bienestar del menor durante su estancia</li>
                                    <li>Cumplimiento de obligaciones legales y regulatorias</li>
                                </ul>
                                <p><strong>No utilizamos la información para:</strong></p>
                                <ul>
                                    <li>Propósitos comerciales o de marketing</li>
                                    <li>Publicidad dirigida</li>
                                    <li>Venta de datos a terceros</li>
                                </ul>
                            <?php endif; ?>
                        </div>
                    </div>

                    <!-- 4. Divulgación a Terceros -->
                    <div class="coppa-subsection">
                        <h3 class="coppa-subsection__title">4. Divulgación a Terceros</h3>
                        <div class="coppa-subsection__content">
                            <?php if (isset($notice->third_party_disclosure)): ?>
                                <?= nl2br(htmlspecialchars($notice->third_party_disclosure)) ?>
                            <?php else: ?>
                                <p>Compartimos información con terceros únicamente en las siguientes circunstancias:</p>
                                <ul>
                                    <li><strong>Proveedores de servicios:</strong> Plataformas tecnológicas necesarias para el funcionamiento del servicio (bajo acuerdos de confidencialidad)</li>
                                    <li><strong>Autoridades legales:</strong> Cuando sea requerido por ley o para proteger la seguridad del menor</li>
                                    <li><strong>Con consentimiento explícito:</strong> Solo con autorización previa y escrita del padre/tutor</li>
                                </ul>
                                <p>Todos los proveedores de servicios están obligados a mantener la confidencialidad y seguridad de los datos.</p>
                            <?php endif; ?>
                        </div>
                    </div>

                    <!-- 5. Derechos del Padre/Tutor -->
                    <div class="coppa-subsection">
                        <h3 class="coppa-subsection__title">5. Derechos del Padre/Tutor</h3>
                        <div class="coppa-subsection__content">
                            <?php if (isset($notice->parent_rights)): ?>
                                <?= nl2br(htmlspecialchars($notice->parent_rights)) ?>
                            <?php else: ?>
                                <p>Como padre o tutor, usted tiene los siguientes derechos:</p>
                                <ul>
                                    <li><strong>Acceso:</strong> Solicitar acceso a toda la información recopilada sobre su menor</li>
                                    <li><strong>Rectificación:</strong> Solicitar corrección de información inexacta</li>
                                    <li><strong>Eliminación:</strong> Solicitar eliminación de información del menor</li>
                                    <li><strong>Revocación:</strong> Revocar el consentimiento en cualquier momento</li>
                                    <li><strong>Oposición:</strong> Oponerse al procesamiento de datos personales</li>
                                </ul>
                                <p>Para ejercer estos derechos, contacte a: <strong>privacy@acuarela.app</strong></p>
                            <?php endif; ?>
                        </div>
                    </div>

                    <!-- 6. Retención y Eliminación -->
                    <div class="coppa-subsection">
                        <h3 class="coppa-subsection__title">6. Retención y Eliminación de Datos</h3>
                        <div class="coppa-subsection__content">
                            <?php if (isset($notice->retention_policy)): ?>
                                <?= nl2br(htmlspecialchars($notice->retention_policy)) ?>
                            <?php else: ?>
                                <p><strong>Período de retención:</strong></p>
                                <ul>
                                    <li>Los datos se conservan mientras el menor esté inscrito en el programa</li>
                                    <li>Después de la desinscripción, los datos se eliminan según los plazos legales aplicables</li>
                                    <li>Algunos registros pueden conservarse por períodos más largos cuando sea requerido por ley</li>
                                </ul>
                                <p><strong>Eliminación:</strong> Al solicitar la eliminación, procederemos a eliminar todos los datos personales del menor, excepto aquellos que debamos conservar por obligaciones legales.</p>
                            <?php endif; ?>
                        </div>
                    </div>

                    <!-- Contenido adicional personalizado -->
                    <?php if (isset($notice->additional_content) && !empty($notice->additional_content)): ?>
                    <div class="coppa-subsection">
                        <div class="coppa-subsection__content">
                            <?= nl2br(htmlspecialchars($notice->additional_content)) ?>
                        </div>
                    </div>
                    <?php endif; ?>

                </section>

                <!-- Información de contacto -->
                <section class="coppa-section coppa-section--contact">
                    <h2 class="coppa-section__title">Contacto</h2>
                    <div class="coppa-section__content">
                        <p>Para preguntas sobre este aviso o para ejercer sus derechos, contacte:</p>
                        <p><strong>Email:</strong> privacy@acuarela.app</p>
                        <p><strong>Versión del aviso:</strong> <?= htmlspecialchars($version) ?></p>
                        <p><strong>Fecha de publicación:</strong> <?= htmlspecialchars($publishedAt) ?></p>
                    </div>
                </section>
            </div>
        </main>

        <footer class="coppa-footer">
            <div class="coppa-footer__content">
                <p>&copy; <?= date('Y') ?> Bilingual Child Care Training (BCCT). Todos los derechos reservados.</p>
                <p><a href="/miembros/acuarela-app-web/" class="coppa-footer__link">Volver a la aplicación</a></p>
            </div>
        </footer>
    </div>
</body>
</html>
