# Guía Visual: Crear Colección COPPA en Strapi

## Paso 1: Acceder al Content-Type Builder

1. En el menú izquierdo de Strapi, busca la sección **"PLUGINS"**
2. Haz clic en **"Creador de Tipos de Contenido"** (Content-Type Builder)
   - Es el que tiene el ícono de checkmark ✓

## Paso 2: Crear Nueva Colección

1. En la parte superior derecha, haz clic en el botón **"+ Crear nuevo tipo de colección"** o **"+ Create new collection type"**
2. En el modal que aparece:
   - **Nombre para mostrar:** `COPPA Notice` o `Aviso COPPA`
   - **Nombre de la API:** `coppa-notice` (Strapi automáticamente lo pluralizará a `coppa-notices`)
3. Haz clic en **"Continuar"**

## Paso 3: Agregar Campos

Agrega los campos uno por uno haciendo clic en **"+ Agregar otro campo"**:

### Campos de Texto (Text):

1. **notice_type**
   - Tipo: Text
   - Nombre: `notice_type`
   - Requerido: ✅ Sí

2. **version**
   - Tipo: Text
   - Nombre: `version`
   - Requerido: ✅ Sí

3. **url**
   - Tipo: Text
   - Nombre: `url`
   - Requerido: ✅ Sí

4. **checksum**
   - Tipo: Text
   - Nombre: `checksum`
   - Requerido: ❌ No (se genera automáticamente)

5. **operator_name**
   - Tipo: Text
   - Nombre: `operator_name`
   - Requerido: ✅ Sí

### Campo de Fecha (Date):

6. **notice_published_date**
   - Tipo: Date
   - Nombre: `notice_published_date`
   - Requerido: ✅ Sí
   - Tipo de fecha: Date (solo fecha)
   
   **⚠️ IMPORTANTE:** No uses `published_at` porque es un campo reservado de Strapi. Usa `notice_published_date` en su lugar.

### Campo de Enumeración (Enumeration):

7. **status**
   - Tipo: Enumeration
   - Nombre: `status`
   - Valores (uno por línea, son texto/strings, NO números):
     ```
     active
     archived
     ```
   - Requerido: ✅ Sí
   - Valor por defecto: `active` (es texto, no un número)
   
   **Nota importante:** En Strapi, los valores de Enumeration son strings (texto). Cuando ingreses los valores, escribe literalmente `active` y `archived` como texto, uno en cada línea. El valor por defecto también es texto: `active`.

### Campos de Texto Enriquecido (Rich Text):

8. **summary**
   - Tipo: Rich text
   - Nombre: `summary`
   - Requerido: ❌ No

9. **operator_contact**
   - Tipo: Rich text
   - Nombre: `operator_contact`
   - Requerido: ❌ No

10. **data_collected**
    - Tipo: Rich text
    - Nombre: `data_collected`
    - Requerido: ❌ No

11. **data_usage**
    - Tipo: Rich text
    - Nombre: `data_usage`
    - Requerido: ❌ No

12. **third_party_disclosure**
    - Tipo: Rich text
    - Nombre: `third_party_disclosure`
    - Requerido: ❌ No

13. **parent_rights**
    - Tipo: Rich text
    - Nombre: `parent_rights`
    - Requerido: ❌ No

14. **retention_policy**
    - Tipo: Rich text
    - Nombre: `retention_policy`
    - Requerido: ❌ No

15. **additional_content**
    - Tipo: Rich text
    - Nombre: `additional_content`
    - Requerido: ❌ No

## Paso 4: Guardar la Colección

1. Una vez agregados todos los campos, haz clic en **"Guardar"** (botón en la parte superior derecha)
2. Strapi reiniciará el servidor automáticamente

## Paso 5: Configurar Permisos Públicos

**IMPORTANTE:** Esto permite que la página pública acceda al aviso sin autenticación.

1. En el menú izquierdo, ve a **"Configuraciones"** (Settings) - el ícono de engranaje ⚙️
2. En el submenú, haz clic en **"Roles"** o **"Roles y Permisos"**
3. Haz clic en **"Public"** (rol público)
4. Busca la sección **"COPPA Notice"** o **"COPPA Notices"**
5. Marca los permisos:
   - ✅ **find** (buscar/lista)
   - ✅ **findOne** (obtener uno)
   - ❌ Deja sin marcar: create, update, delete
6. Haz clic en **"Guardar"**

## Paso 6: Crear Primera Versión del Aviso

1. En el menú izquierdo, ve a **"Content Manager"**
2. Busca **"COPPA Notices"** en la lista de tipos de colección
3. Haz clic en **"+ Crear nueva entrada"**
4. Llena los campos:

   **Campos básicos:**
   - `notice_type`: `COPPA`
   - `version`: `v1.0`
   - `status`: `active`
   - `notice_published_date`: Selecciona la fecha de hoy
   - `url`: `/privacy/coppa`
   - `operator_name`: `Bilingual Child Care Training (BCCT)`

   **Campos de contenido:**
   - Llena `summary`, `operator_contact`, `data_collected`, `data_usage`, `third_party_disclosure`, `parent_rights`, `retention_policy` con el contenido del aviso COPPA

   **Checksum:**
   - Puedes dejarlo vacío por ahora o generar uno manualmente
   - Para generar: concatena todo el contenido y usa un generador SHA-256 online

5. Haz clic en **"Guardar"** y luego en **"Publicar"**

## Verificación

Para verificar que todo funciona:

1. Ve a: `https://tu-dominio.com/miembros/acuarela-app-web/privacy/coppa`
2. Deberías ver la página del Aviso COPPA con el contenido que creaste
3. En el formulario de inscripción, el checkbox debería cargar la versión automáticamente

## Notas Importantes

- **Solo puede haber UNA versión con `status: active`** a la vez
- Para crear una nueva versión, primero cambia la anterior a `archived`
- El campo `checksum` es opcional pero recomendado para auditoría
- Los campos Rich Text permiten formato HTML básico

## Troubleshooting

**Error: "No se pudo cargar el aviso COPPA"**
- Verifica que los permisos públicos estén configurados (Paso 5)
- Verifica que haya al menos una entrada con `status: active`
- Verifica que la entrada esté publicada (no en borrador)

**El formulario no carga la versión:**
- Abre la consola del navegador (F12) y revisa errores
- Verifica que el endpoint `/get/getCoppaNotice.php` funcione correctamente
- Verifica que la API de Strapi esté accesible
