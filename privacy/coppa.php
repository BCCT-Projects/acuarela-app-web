<?php
// Página pública del Aviso COPPA - No requiere autenticación
require_once __DIR__ . '/../includes/env.php';

// Función para obtener aviso COPPA sin autenticación
function getPublicCoppaNotice()
{
    $domain = Env::get('ACUARELA_API_URL', 'https://acuarelacore.com/api/');
    // Usar el nombre correcto del endpoint: aviso-coppas (plural)
    $endpoint = $domain . 'aviso-coppas?status=active&_sort=notice_published_date:DESC&_limit=1';

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

    if ($httpCode === 200) {
        $data = json_decode($response, true);

        // Strapi devuelve un array directo, no dentro de 'data' o 'response'
        if (is_array($data) && !empty($data)) {
            // Convertir array a objeto con estructura 'response'
            return (object) ['response' => array_map(function ($item) {
                return (object) $item; }, $data)];
        }
    } else {
        error_log("COPPA Notice API Error (endpoint: $endpoint): HTTP $httpCode - $error");
    }

    return null;
}

// Obtener versión activa del aviso COPPA
$coppaNotice = getPublicCoppaNotice();

// Si no hay versión activa, mostrar mensaje
if (!$coppaNotice || !isset($coppaNotice->response) || empty($coppaNotice->response)) {
    http_response_code(404);
    die('Aviso COPPA no disponible. Por favor contacte al administrador.');
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
    <link rel="shortcut icon" href="../img/favicon.png">
    <style>
        /* CRITICAL: Fix Scroll & Layout */
        html {
            overflow-y: auto !important;
            height: auto !important;
        }

        /* DESIGN SYSTEM VARIABLES */
        :root {
            /* Paleta Primaria */
            --cielo: #0CB5C3;
            /* Turquesa */
            --sandia: #FA6F5C;
            /* Rojo suave */
            --pollito: #FBCB43;
            /* Amarillo */
            --morita: #7155A4;
            /* Morado */

            /* Colores Fundamentales */
            --blanco: #FFFFFF;
            --fondo1: #F0FEFF;
            /* Light Cyan Background */
            --fondo2: #E8F7F9;
            /* Alterative background */
            --gris1: #140A4C;
            /* Main Text */
            --gris2: #4A4A68;
            /* Secondary Text */
            --gris3: #9EA0A5;
            /* Placeholder/Disabled */
        }

        /* Reset & Override global App styles */
        body.coppa-page {
            display: block !important;
            margin: 0;
            padding: 0;
            width: 100%;
            max-width: 100%;
            height: auto !important;
            overflow-y: auto !important;
            position: relative !important;

            /* Base Font */
            background: var(--fondo1);
            font-family: 'Outfit', 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            color: var(--gris1);
            line-height: 1.6;
        }

        .coppa-container {
            display: flex;
            flex-direction: column;
            min-height: 100vh;
            width: 100%;
        }

        /* TYPOGRAPHY RULES (Directly from Design System) */

        /* Display / H1 */
        h1,
        .display,
        .coppa-header__title {
            font-size: 2.7rem;
            font-style: normal;
            font-weight: bold;
            line-height: 3.6rem;
            color: #ffffff !important;
            /* Header specific */
        }

        @media only screen and (min-width: 768px) {

            h1,
            .display,
            .coppa-header__title {
                font-size: 2.7rem;
                line-height: 3.6rem;
            }
        }

        /* Título / H2 */
        h2,
        .title,
        .coppa-section__title {
            font-size: 2.1rem;
            font-style: normal;
            font-weight: bold;
            line-height: 2.7rem;
            color: var(--morita);
        }

        @media only screen and (min-width: 768px) {

            h2,
            .title,
            .coppa-section__title {
                font-size: 2.1rem;
                line-height: 2.7rem;
            }
        }

        /* Subtítulo / H3 */
        h3,
        .subtitle,
        .coppa-subsection__title {
            font-size: 1.8rem;
            font-style: normal;
            font-weight: bold;
            line-height: 2.4rem;
            color: var(--cielo);
        }

        @media only screen and (min-width: 768px) {

            h3,
            .subtitle,
            .coppa-subsection__title {
                font-size: 1.8rem;
                line-height: 2.4rem;
            }
        }

        /* Énfasis / H4 */
        h4,
        .enfasis {
            font-size: 1.6rem;
            font-style: normal;
            font-weight: bold;
            line-height: 2rem;
        }

        @media only screen and (min-width: 768px) {

            h4,
            .enfasis {
                font-size: 1.6rem;
                line-height: 2rem;
            }
        }

        /* Texto Regular */
        p,
        li,
        .regular,
        .coppa-section__content,
        .coppa-subsection__content {
            font-size: 1.4rem;
            font-style: normal;
            font-weight: normal;
            line-height: 2rem;
            margin-bottom: 15px;
            color: var(--gris2);
        }

        @media only screen and (min-width: 768px) {

            p,
            li,
            .regular,
            .coppa-section__content,
            .coppa-subsection__content {
                font-size: 1.4rem;
                line-height: 2rem;
            }
        }

        /* Texto Bold */
        .bold,
        b,
        strong {
            font-size: 1.4rem;
            /* Should match regular but bold */
            font-style: normal;
            font-weight: bold;
            line-height: 2rem;
            color: var(--gris1);
            /* Emphasize with darker grey */
        }

        /* Caption */
        .caption,
        small,
        .coppa-footer {
            font-size: 1.2rem;
            font-style: normal;
            font-weight: normal;
            line-height: 1.4rem;
            color: var(--gris3);
        }

        /* COMPONENT STYLES */

        /* Header */
        .coppa-header {
            background: linear-gradient(135deg, var(--morita) 0%, var(--cielo) 100%);
            padding: 5rem 2rem 8rem;
            text-align: center;
            position: relative;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
            z-index: 10;
        }

        .coppa-header::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background-image: url('data:image/svg+xml,<svg width="20" height="20" xmlns="http://www.w3.org/2000/svg"><circle cx="2" cy="2" r="2" fill="white" opacity="0.1"/></svg>');
            pointer-events: none;
        }

        .coppa-header__content {
            max-width: 900px;
            margin: 0 auto;
            position: relative;
            z-index: 2;
        }

        .coppa-header__logo {
            max-width: 200px;
            margin-bottom: 2rem;
            filter: brightness(0) invert(1);
        }

        .coppa-header__subtitle {
            font-size: 1.8rem !important;
            /* Subtitle size */
            font-weight: normal;
            color: rgba(255, 255, 255, 0.9);
            margin-top: 5px;
        }

        /* Main Card */
        .coppa-main {
            padding: 0 20px 4rem;
            width: 100%;
            max-width: 1000px;
            margin: -5rem auto 0;
            position: relative;
            z-index: 20;
        }

        .coppa-notice {
            background: var(--blanco);
            border-radius: 24px;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.08);
            padding: 4rem;
        }

        /* Specific Overrides for Layout Spacing */
        .coppa-section {
            margin-bottom: 4rem;
        }

        .coppa-section__title {
            margin: 3rem 0 2rem;
            border-bottom: 2px solid var(--fondo2);
            padding-bottom: 1rem;
        }

        .coppa-subsection {
            margin-bottom: 2.5rem;
        }

        /* Meta Box */
        .coppa-notice__meta {
            background: var(--fondo2);
            padding: 2rem;
            border-radius: 12px;
            border-left: 6px solid var(--cielo);
            margin-bottom: 3rem;
            display: flex;
            flex-wrap: wrap;
            gap: 2rem;
            align-items: center;
        }

        .coppa-notice__version strong,
        .coppa-notice__date strong {
            color: var(--morita);
        }

        /* Lists */
        ul {
            padding-left: 0;
            margin-bottom: 2rem;
        }

        li {
            padding-left: 2rem;
            margin-bottom: 1rem;
            position: relative;
        }

        li::before {
            content: "•";
            color: var(--sandia);
            font-weight: bold;
            font-size: 1.5em;
            /* Slightly larger for bullet */
            position: absolute;
            left: 0;
            top: -5px;
        }

        /* Summary & Contact */
        .coppa-section--summary {
            background: rgba(12, 181, 195, 0.05);
            /* Cielo with opacity */
            border: 1px solid rgba(12, 181, 195, 0.1);
            border-radius: 16px;
            padding: 2.5rem;
        }

        .coppa-contact {
            background: var(--fondo2);
            padding: 2.5rem;
            border-radius: 16px;
            border-top: 5px solid var(--pollito);
            margin-top: 3rem;
        }

        a {
            color: var(--cielo);
            text-decoration: none;
            font-weight: bold;
        }

        a:hover {
            color: var(--morita);
        }

        /* Footer */
        .coppa-footer {
            text-align: center;
            padding: 4rem 1rem;
        }

        /* Responsive Overrides */
        @media (max-width: 768px) {
            .coppa-header {
                padding: 4rem 1.5rem 6rem;
            }

            .coppa-notice {
                padding: 2rem;
                border-radius: 16px;
            }

            .coppa-main {
                padding: 0 15px 3rem;
                margin-top: -4rem;
            }
        }
    </style>
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
                    <p class="coppa-notice__date">Fecha de publicación:
                        <strong><?= htmlspecialchars($publishedAt) ?></strong></p>
                    <?php if (isset($notice->checksum)): ?>
                        <p class="coppa-notice__checksum">ID de verificación:
                            <code><?= substr($notice->checksum, 0, 16) ?>...</code></p>
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
                                    <li><strong>Información de identificación:</strong> Nombre completo, fecha de
                                        nacimiento, género</li>
                                    <li><strong>Información educativa:</strong> Actividades, asistencia, progreso académico,
                                        fotografías y videos de actividades</li>
                                    <li><strong>Información de salud:</strong> Alergias, condiciones médicas, medicamentos
                                        (cuando sea necesario para el cuidado)</li>
                                    <li><strong>Información de contacto:</strong> Datos de padres/tutores y contactos de
                                        emergencia</li>
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
                                    <li><strong>Proveedores de servicios:</strong> Plataformas tecnológicas necesarias para
                                        el funcionamiento del servicio (bajo acuerdos de confidencialidad)</li>
                                    <li><strong>Autoridades legales:</strong> Cuando sea requerido por ley o para proteger
                                        la seguridad del menor</li>
                                    <li><strong>Con consentimiento explícito:</strong> Solo con autorización previa y
                                        escrita del padre/tutor</li>
                                </ul>
                                <p>Todos los proveedores de servicios están obligados a mantener la confidencialidad y
                                    seguridad de los datos.</p>
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
                                    <li><strong>Acceso:</strong> Solicitar acceso a toda la información recopilada sobre su
                                        menor</li>
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
                                    <li>Después de la desinscripción, los datos se eliminan según los plazos legales
                                        aplicables</li>
                                    <li>Algunos registros pueden conservarse por períodos más largos cuando sea requerido
                                        por ley</li>
                                </ul>
                                <p><strong>Eliminación:</strong> Al solicitar la eliminación, procederemos a eliminar todos
                                    los datos personales del menor, excepto aquellos que debamos conservar por obligaciones
                                    legales.</p>
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