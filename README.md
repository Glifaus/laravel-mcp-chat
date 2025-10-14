# Laravel MCP Chat

Aplicación de chat minimalista construida con Laravel 12 y el servidor MCP (Model Context Protocol). Permite enviar mensajes, responder en hilos, organizar conversaciones por canales, reaccionar con emojis, buscar y listar usuarios; todo accesible desde herramientas MCP listas para usar.

Requisitos: PHP 8.4+, SQLite, Node.js (para assets), Composer y npm.

Nota: Este proyecto parte del esqueleto Laravel Starter Kit y añade un servidor MCP completo orientado a chat.

## 🚀 Características

- Envío y listado de mensajes recientes
- Respuestas (threads) y visualización de hilos completos
- Canales/rooms con herencia automática de canal en respuestas
- Reacciones con emojis (anti-duplicados por usuario/emoji/mensaje)
- Búsqueda por palabra clave, por usuario y por rango de fechas
- Listado de usuarios activos con estadísticas
- Tests con Pest, análisis estático con PHPStan y formateo con Pint/Rector

## 🧩 Herramientas MCP disponibles

Nombre de herramienta (parámetros → descripción breve):

- [send-message] (name, content, channel?) → Enviar mensaje; canal por defecto: general
- [get-messages] (limit?) → Últimos mensajes (por defecto 50)
- [reply-to-message] (parent_message_id, name, content) → Responder y crear hilo; hereda canal del padre
- [get-message-thread] (message_id) → Ver mensaje y todas sus respuestas cronológicas
- [get-channels] () → Listado de canales con estadísticas (cuenta, última actividad)
- [get-channel-messages] (channel, limit?) → Mensajes principales de un canal
- [add-reaction] (message_id, user_name, emoji) → Añadir reacción; 1 por emoji/usuario/mensaje
- [remove-reaction] (message_id, user_name, emoji) → Quitar tu reacción
- [get-message-reactions] (message_id) → Reacciones agrupadas por emoji y quién reaccionó
- [get-users-list] (limit?, sort_by?) → Usuarios únicos con total de mensajes y última actividad
- [search-messages] (query, limit?) → Buscar por palabra/frase (insensible a mayúsculas)
- [get-messages-by-user] (name, limit?) → Filtrar por autor (coincidencia parcial)
- [get-messages-by-date-range] (start_date?, end_date?, limit?) → Filtrar por fechas

Parámetros comunes: strings con límites de longitud (name: 1-50, content: 1-500, channel: <=50). Límite de resultados: 1-100 (default 50).

Emojis permitidos en herramientas de reacciones: 👍 ❤️ 😂 🎉 🚀 👏 🔥 💯 👎 😮 😢 😡 🤔 💡 ✅ ❌

## 🧪 Ejemplos rápidos (cliente MCP)

- Enviar mensaje a un canal:
  {
  "name": "Alice",
  "content": "Hola desde Python!",
  "channel": "python"
  }

- Responder a un mensaje:
  {
  "parent_message_id": 42,
  "name": "Bob",
  "content": "Totalmente de acuerdo"
  }

- Ver hilo:
  { "message_id": 42 }

- Listar canales:
  {}

- Mensajes del canal:
  { "channel": "general", "limit": 10 }

- Añadir reacción:
  { "message_id": 1, "user_name": "Jane", "emoji": "👍" }

- Buscar mensajes:
  { "query": "Laravel", "limit": 20 }

## 🛠️ Puesta en marcha local

1. Dependencias y entorno

- Copia .env y genera key
- Asegúrate de tener SQLite disponible

2. Instalación y build

- composer install
- npm install
- npm run build (o npm run dev)

3. Base de datos

- Crear archivo SQLite: database/database.sqlite (si no existe)
- php artisan migrate
- Opcional: poblar dataset de ejemplo Knowmadmood
    - php artisan db:seed --class=Database\\Seeders\\KnowmadmoodSeeder

4. Servidor

- php artisan serve

5. Endpoint MCP

- Disponible en: /mcp/chat

Consejos

- Si no ves cambios de frontend, ejecuta npm run dev o npm run build
- Scripts útiles: composer test, composer lint, composer test:types

## 📖 Guía de uso por feature

### Mensajes

- Envío con [send-message] (canal opcional; por defecto general)
- Listado con [get-messages]

### Threads/Hilos

- Crear respuesta con [reply-to-message] (valida existencia del padre)
- Ver hilo con [get-message-thread] (incluye conteo, orden cronológico y reacciones)

### Canales

- Campo channel indexado; máximo 50 caracteres
- Respuestas heredan automáticamente el canal del padre
- Herramientas: [get-channels], [get-channel-messages], [send-message] (channel), [reply-to-message] (hereda)

### Reacciones y Usuarios

- Reacciones: [add-reaction], [remove-reaction], [get-message-reactions]
- Usuarios: [get-users-list] con ordenación por name, messages (default) o last_activity

### Búsqueda y filtros

- Palabra clave: [search-messages]
- Por usuario: [get-messages-by-user]
- Rango de fechas: [get-messages-by-date-range]

## 🗄️ Modelo de datos y relaciones

- Message: id, parent_id (nullable, FK self), name, content, channel (index), timestamps
- Reaction: id, message_id (FK), user_name, emoji, timestamps, UNIQUE(message_id, user_name, emoji)
- Relaciones:
    - Message hasMany replies (parent_id)
    - Message belongsTo parent
    - Message hasMany reactions
    - Reaction belongsTo Message

Consultas típicas optimizadas:

- Mensajes por canal (usa índice channel): WHERE channel = ? AND parent_id IS NULL
- Listado de canales: GROUP BY channel con MAX(created_at) y COUNT(\*)

## 🧰 Desarrollo, calidad y tests

- Lint y formateo: Pint y Rector → composer lint
- Type coverage (Pest): composer test:type-coverage
- Análisis estático (PHPStan): composer test:types
- Tests unitarios (Pest): composer test:unit
- Suite completa: composer test

Los tests cubren: threads, canales, reacciones, usuarios, búsquedas y filtros, validaciones y formatos de salida (timestamps relativos, singular/plural correcto, límites de resultados).

## 📦 Datos de ejemplo (opcional)

Seeder Knowmadmood crea mensajes, hilos, reacciones y múltiples canales realistas:

- Comando: php artisan db:seed --class=Database\\Seeders\\KnowmadmoodSeeder
- Canales ejemplo: general, jobs, php, python, devops, off-topic
- Incluye hilos y distintas reacciones en mensajes principales y respuestas

## 🌐 Acceso web y utilidades

- Servidor local: php artisan serve → http://localhost:8000
- Endpoint MCP: http://localhost:8000/mcp/chat
- SQL/CLI útil: ver HOW_TO_VIEW.md para consultas con sqlite3 y alias rápidos

## 🔭 Siguientes pasos sugeridos

- Gestión explícita de canales (crear/renombrar/eliminar, descripciones, privados)
- Búsqueda por canal específico en herramientas de búsqueda
- Notificaciones/subscripciones a hilos y canales
- Estadísticas avanzadas (trending, top reactores, emojis más usados)

## 📄 Licencia

MIT. Basado en Laravel Starter Kit y extendido para un servidor MCP de chat.
