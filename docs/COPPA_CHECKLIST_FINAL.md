# Checklist Final - Implementación Aviso COPPA

## ✅ Pasos Completados

- [x] Estructura de código creada (página pública, endpoints, SDK)
- [x] Integración en formulario de inscripción
- [x] Colección creada en Strapi
- [x] Contenido preparado

## 🔄 Pasos Pendientes

### 1. Llenar Contenido en Strapi

- [ ] Abrir "Aviso COPPAS" en Content Manager
- [ ] Hacer clic en "+ Agregar Aviso COPPAS"
- [ ] Llenar campos básicos:
  - [ ] `notice_type`: `COPPA`
  - [ ] `version`: `v1.0`
  - [ ] `status`: Seleccionar `active`
  - [ ] `notice_published_date`: Fecha de hoy
  - [ ] `url`: `/privacy/coppa`
  - [ ] `operator_name`: `Bilingual Child Care Training (BCCT)`
- [ ] Copiar y pegar contenido de `COPPA_CONTENIDO_EJEMPLO.md` en cada campo:
  - [ ] `summary` (Resumen ejecutivo)
  - [ ] `operator_contact` (Información de contacto)
  - [ ] `data_collected` (Datos recopilados)
  - [ ] `data_usage` (Uso de la información)
  - [ ] `third_party_disclosure` (Divulgación a terceros)
  - [ ] `parent_rights` (Derechos del padre/tutor)
  - [ ] `retention_policy` (Retención y eliminación)
  - [ ] `additional_content` (Opcional)
- [ ] Guardar la entrada
- [ ] Publicar la entrada (si está en modo borrador)

### 2. Configurar Permisos Públicos (CRÍTICO)

**⚠️ SIN ESTO, LA PÁGINA PÚBLICA NO FUNCIONARÁ**

- [ ] Ir a **Configuraciones** (⚙️) en el menú izquierdo
- [ ] Hacer clic en **"Roles"** o **"Roles y Permisos"**
- [ ] Seleccionar **"Public"** (rol público)
- [ ] Buscar la sección **"COPPA Notice"** o **"Aviso COPPA"**
- [ ] Marcar los siguientes permisos:
  - [ ] ✅ **find** (buscar/lista)
  - [ ] ✅ **findOne** (obtener uno específico)
  - [ ] ❌ **NO marcar**: create, update, delete
- [ ] Hacer clic en **"Guardar"**

### 3. Compilar Estilos SCSS (si no es automático)

- [ ] Verificar si hay un proceso automático de compilación SCSS
- [ ] Si no es automático, compilar manualmente:
  ```bash
  # Ejemplo (ajustar según tu setup)
  sass scss/styles.scss css/styles.css
  ```
- [ ] Verificar que `css/coppa.css` o el CSS compilado incluya los estilos

### 4. Pruebas de Funcionalidad

#### 4.1 Probar Página Pública

- [ ] Abrir en navegador: `https://tu-dominio.com/miembros/acuarela-app-web/privacy/coppa`
- [ ] Verificar que la página carga correctamente
- [ ] Verificar que muestra:
  - [ ] Versión del aviso (v1.0)
  - [ ] Fecha de publicación
  - [ ] Todas las secciones del contenido
  - [ ] Enlaces funcionan correctamente
- [ ] Probar en modo incógnito (sin login) para verificar acceso público

#### 4.2 Probar Endpoint API

- [ ] Abrir: `https://tu-dominio.com/miembros/acuarela-app-web/get/getCoppaNotice.php`
- [ ] Verificar que retorna JSON con `success: true`
- [ ] Verificar que incluye `data` con la información del aviso
- [ ] Verificar que `data.version` es "v1.0"
- [ ] Verificar que `data.status` es "active"

#### 4.3 Probar Formulario de Inscripción

- [ ] Iniciar sesión en la aplicación
- [ ] Ir a formulario de inscripción (`/inscripciones`)
- [ ] Verificar que aparece la pestaña **"Consentimiento COPPA"**
- [ ] Hacer clic en la pestaña
- [ ] Verificar que:
  - [ ] Aparece el checkbox de consentimiento
  - [ ] El enlace al aviso funciona
  - [ ] Muestra la versión del aviso (ej: "v1.0")
- [ ] Intentar enviar formulario sin marcar checkbox:
  - [ ] Debe mostrar error
  - [ ] Debe pedir aceptar el consentimiento
- [ ] Marcar checkbox y enviar:
  - [ ] Debe permitir enviar
  - [ ] Verificar en la base de datos que se guardó:
    - [ ] `coppa_notice_version`: "v1.0"
    - [ ] `coppa_consent`: true

#### 4.4 Probar Enlace en Configuración

- [ ] Ir a página de configuración
- [ ] Verificar que aparece enlace "Aviso de Privacidad COPPA" en el footer
- [ ] Hacer clic en el enlace
- [ ] Verificar que abre la página del aviso en nueva pestaña

### 5. Verificaciones de Seguridad

- [ ] Verificar que la página pública es accesible sin autenticación
- [ ] Verificar que solo se puede leer (find/findOne), no crear/modificar/eliminar
- [ ] Verificar que el formulario valida el consentimiento antes de enviar
- [ ] Verificar que se guarda la versión del aviso aceptada

### 6. Documentación y Registro

- [ ] Guardar captura de pantalla de la página pública del aviso
- [ ] Guardar captura del formulario con el checkbox
- [ ] Documentar la fecha de publicación del aviso v1.0
- [ ] Registrar en logs/documentación que el sistema COPPA está activo

## 🐛 Troubleshooting

### Problema: La página pública muestra "Aviso COPPA no disponible"

**Soluciones:**
1. Verificar que la entrada en Strapi esté **publicada** (no en borrador)
2. Verificar que `status` sea `active`
3. Verificar permisos públicos (Paso 2)
4. Verificar que la API de Strapi esté accesible
5. Revisar consola del navegador para errores

### Problema: El formulario no carga la versión

**Soluciones:**
1. Abrir consola del navegador (F12)
2. Verificar errores en la pestaña Console
3. Verificar que `/get/getCoppaNotice.php` retorna datos correctos
4. Verificar que el campo `coppa_notice_version` existe en el formulario

### Problema: No puedo acceder sin login

**Soluciones:**
1. Verificar permisos públicos en Strapi (Paso 2)
2. Verificar que la URL sea correcta
3. Probar en modo incógnito
4. Verificar que no hay redirecciones forzadas a login

## ✅ Criterios de Aceptación Final

- [ ] ✅ El aviso COPPA es accesible públicamente sin login
- [ ] ✅ El contenido cumple con los requisitos COPPA §312.4
- [ ] ✅ El aviso está versionado (v1.0)
- [ ] ✅ El consentimiento parental referencia la versión
- [ ] ✅ El aviso no puede modificarse sin cambiar versión
- [ ] ✅ Existe historial de versiones (preparado para futuras versiones)
- [ ] ✅ El formulario valida el consentimiento antes de enviar
- [ ] ✅ Se guarda la versión aceptada en las inscripciones

## 📝 Notas Finales

- **Fecha de implementación:** _______________
- **Versión inicial:** v1.0
- **Responsable:** _______________
- **URL del aviso:** `/miembros/acuarela-app-web/privacy/coppa`

## 🎯 Próximos Pasos (Futuro)

- [ ] Implementar logging de consentimientos
- [ ] Crear script para generar checksum automáticamente
- [ ] Implementar exportación de datos para auditoría
- [ ] Configurar notificaciones cuando se publique nueva versión
- [ ] Planificar proceso de actualización de versiones

---

**Una vez completados todos los pasos, el sistema COPPA estará completamente funcional y cumplirá con los requisitos legales.**
