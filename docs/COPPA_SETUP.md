# Configuración del Aviso COPPA

## Descripción
Este documento describe cómo configurar y gestionar el sistema de Aviso de Privacidad COPPA para cumplir con los requisitos legales de protección de datos de menores.

## Estructura en Strapi

### Colección: `coppa-notices`

La colección debe tener los siguientes campos:

#### Campos requeridos:
- **notice_type** (Text): Tipo de aviso (debe ser "COPPA")
- **version** (Text): Versión del aviso (ej: "v1.0", "v1.1")
- **notice_published_date** (Date): Fecha de publicación
  - ⚠️ **NO usar `published_at`** (es campo reservado de Strapi)
- **status** (Enumeration): Estado del aviso
  - Opciones: `active`, `archived`
- **url** (Text): URL del aviso (ej: "/privacy/coppa")
- **checksum** (Text): Hash del contenido para verificación de integridad

#### Campos de contenido:
- **summary** (Rich Text): Resumen ejecutivo del aviso
- **operator_name** (Text): Nombre legal del operador (BCCT)
- **operator_contact** (Rich Text): Información de contacto del operador
- **data_collected** (Rich Text): Descripción de datos recopilados
- **data_usage** (Rich Text): Descripción del uso de datos
- **third_party_disclosure** (Rich Text): Información sobre divulgación a terceros
- **parent_rights** (Rich Text): Derechos del padre/tutor
- **retention_policy** (Rich Text): Política de retención y eliminación
- **additional_content** (Rich Text): Contenido adicional opcional

### Configuración inicial

1. **Crear la colección en Strapi:**
   - Ir a Content-Type Builder
   - Crear nueva colección: `coppa-notices`
   - Agregar los campos mencionados arriba

2. **Configurar permisos:**
   - En Settings > Roles > Public: Permitir `find` y `findOne` para `coppa-notices`
   - Esto permite acceso público al aviso sin autenticación

3. **Crear primera versión del aviso:**
   - Ir a Content Manager > COPPA Notices
   - Crear nuevo registro con:
     - `notice_type`: "COPPA"
     - `version`: "v1.0"
     - `status`: "active"
     - `notice_published_date`: Fecha actual
     - `url`: "/privacy/coppa"
     - Llenar todos los campos de contenido

4. **Generar checksum:**
   - El checksum debe ser un hash (SHA-256 recomendado) del contenido completo
   - Puede generarse concatenando todos los campos de contenido y aplicando hash
   - Ejemplo en PHP:
     ```php
     $content = $operator_name . $data_collected . $data_usage . ...;
     $checksum = hash('sha256', $content);
     ```

## Flujo de versionado

### Crear nueva versión:

1. **Archivar versión anterior:**
   - Cambiar `status` de la versión actual a `archived`
   - Esto mantiene el historial para auditoría

2. **Crear nueva versión:**
   - Crear nuevo registro con `version` incrementada (ej: "v1.1")
   - Establecer `status` como `active`
   - Actualizar `notice_published_date` a fecha actual
   - Generar nuevo `checksum`

3. **Invalidar consentimientos (opcional pero recomendado):**
   - Si se hacen cambios significativos, considerar invalidar consentimientos previos
   - Esto requiere solicitar nuevo consentimiento a los padres

## Integración con formularios

### Formulario de inscripción

El formulario de inscripción (`inscripcion.php`) incluye:
- Checkbox de consentimiento COPPA (requerido)
- Carga automática de la versión activa del aviso
- Validación antes de enviar el formulario
- Guardado de `coppa_notice_version` y `coppa_consent` en la inscripción

### Campos guardados en inscripción:

- **coppa_notice_version**: Versión del aviso aceptada
- **coppa_consent**: Boolean indicando si se otorgó consentimiento

## Endpoints API

### GET `/get/getCoppaNotice.php`
Obtiene la versión activa del aviso COPPA.

**Parámetros opcionales:**
- `version`: Obtener versión específica (ej: `?version=v1.0`)

**Respuesta:**
```json
{
  "success": true,
  "data": {
    "id": "...",
    "version": "v1.0",
    "notice_published_date": "2026-01-XX",
    "status": "active",
    ...
  }
}
```

## Página pública

### URL: `/privacy/coppa`

- Accesible sin autenticación
- Muestra la versión activa del aviso
- Incluye información de versión y checksum
- Diseño responsive y accesible

## Enlaces

El aviso COPPA está disponible desde:
1. Página de configuración (`configuracion.php`) - Enlace en footer
2. Formulario de inscripción - Enlace en checkbox de consentimiento
3. URL directa: `/miembros/acuarela-app-web/privacy/coppa`

## Auditoría

### Registros requeridos:

1. **Versión del aviso:**
   - Cada cambio debe crear nueva versión
   - Versiones anteriores deben archivarse (no eliminarse)

2. **Consentimientos:**
   - Cada inscripción debe registrar:
     - Versión del aviso aceptada
     - Fecha de consentimiento
     - IP del padre/tutor (opcional pero recomendado)

3. **Logs de cambios:**
   - Registrar cuando se publica nueva versión
   - Registrar cuando se archiva versión anterior

## Checklist de cumplimiento COPPA

- [x] Aviso público accesible sin login
- [x] Versión del aviso visible
- [x] Contenido completo según COPPA §312.4
- [x] Consentimiento referenciado a versión específica
- [x] Historial de versiones mantenido
- [x] Checksum para verificación de integridad
- [x] Enlaces desde formularios relacionados
- [ ] Logging de consentimientos (pendiente implementar)
- [ ] Exportación de datos para auditoría (pendiente implementar)

## Notas importantes

1. **No eliminar versiones:** Siempre archivar, nunca eliminar versiones anteriores
2. **HTTPS obligatorio:** El aviso debe servirse solo sobre HTTPS
3. **Actualización de consentimientos:** Si se cambia el aviso significativamente, considerar solicitar nuevo consentimiento
4. **Backup regular:** Mantener backups de todas las versiones del aviso

## Soporte

Para preguntas sobre la implementación del Aviso COPPA, contactar al equipo de desarrollo.
