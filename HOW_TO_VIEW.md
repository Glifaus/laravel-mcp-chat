# 📊 Guía Rápida: Cómo Mostrar Todo el Contenido

## ✅ Base de datos poblada exitosamente

- **18 mensajes** sobre Knowmadmood, PHP y Python
- **28 reacciones** distribuidas en los mensajes
- **18 usuarios únicos** participando

---

## 🚀 MÉTODO 1: Usar el Servidor MCP (Recomendado)

Si tienes el servidor MCP activado en tu cliente (Claude Desktop, Cursor, etc.):

### 📝 Ver todos los mensajes

```json
Herramienta: get-messages
Parámetros:
{
  "limit": 50
}
```

### 👥 Ver lista de usuarios

```json
Herramienta: get-users-list
Parámetros:
{
  "sort_by": "messages",
  "limit": 50
}
```

### 🔍 Buscar mensajes sobre Laravel

```json
Herramienta: search-messages
Parámetros:
{
  "query": "Laravel",
  "limit": 50
}
```

### 🔍 Buscar mensajes sobre Python

```json
Herramienta: search-messages
Parámetros:
{
  "query": "Python",
  "limit": 50
}
```

### 👤 Ver mensajes de un usuario específico

```json
Herramienta: get-messages-by-user
Parámetros:
{
  "name": "Carlos",
  "limit": 50
}
```

### 😊 Ver reacciones de un mensaje

```json
Herramienta: get-message-reactions
Parámetros:
{
  "message_id": 1
}
```

---

## 💻 MÉTODO 2: Línea de Comandos

### Ver todos los mensajes con SQL

```bash
cd /Users/glifaus/Code/Sites/laravel-mcp-chat

# Ver todos los mensajes
sqlite3 database/database.sqlite "SELECT id, name, substr(content, 1, 50) || '...' as content FROM messages ORDER BY created_at DESC;"

# Ver mensajes con formato bonito
sqlite3 database/database.sqlite -column -header "SELECT id, name, substr(content, 1, 80) as content FROM messages ORDER BY created_at DESC LIMIT 10;"
```

### Ver estadísticas

```bash
# Contar mensajes
sqlite3 database/database.sqlite "SELECT COUNT(*) as total_messages FROM messages;"

# Contar usuarios únicos
sqlite3 database/database.sqlite "SELECT COUNT(DISTINCT name) as unique_users FROM messages;"

# Contar reacciones
sqlite3 database/database.sqlite "SELECT COUNT(*) as total_reactions FROM reactions;"

# Ver reacciones por mensaje
sqlite3 database/database.sqlite -column -header "SELECT m.id, m.name, COUNT(r.id) as reactions FROM messages m LEFT JOIN reactions r ON m.id = r.message_id GROUP BY m.id ORDER BY reactions DESC;"
```

### Ver usuarios más activos

```bash
sqlite3 database/database.sqlite -column -header "SELECT name, COUNT(*) as messages FROM messages GROUP BY name ORDER BY messages DESC;"
```

---

## 🌐 MÉTODO 3: Acceso Web (si tienes servidor corriendo)

Si tienes el servidor Laravel corriendo:

```bash
# Iniciar servidor
php artisan serve
```

Luego accede a:

- **Ruta MCP**: `http://localhost:8000/mcp/chat`
- **Página principal**: `http://localhost:8000`

O si usas Laravel Herd/Valet:

- **Ruta MCP**: `http://laravel-mcp-chat.test/mcp/chat`

---

## 📋 MÉTODO 4: Laravel Tinker (Interactivo)

```bash
php artisan tinker
```

Dentro de tinker:

```php
// Ver todos los mensajes
Message::all()->map(fn($m) => ['id' => $m->id, 'name' => $m->name, 'content' => substr($m->content, 0, 50)]);

// Ver últimos 10 mensajes
Message::latest()->limit(10)->get()->map(fn($m) => ['name' => $m->name, 'content' => $m->content]);

// Ver usuarios únicos
Message::select('name')->distinct()->pluck('name');

// Ver mensaje con sus reacciones
$message = Message::with('reactions')->find(1);
echo "Mensaje: " . $message->content . "\n";
echo "Reacciones: " . $message->reactions->count() . "\n";
$message->reactions->each(fn($r) => echo "- {$r->emoji} por {$r->user_name}\n");

// Ver todas las reacciones agrupadas
Reaction::selectRaw('emoji, COUNT(*) as count')->groupBy('emoji')->get();
```

---

## 🎯 CONSULTAS ÚTILES PREDEFINIDAS

### Ver resumen completo

```bash
# Crear archivo temporal con consultas
cat > /tmp/show_all.sql << 'EOF'
.mode column
.headers on
.width 5 15 80

SELECT '=== MENSAJES ===' as '';
SELECT id, name, content FROM messages ORDER BY created_at DESC;

SELECT '' as '';
SELECT '=== USUARIOS ===' as '';
SELECT name, COUNT(*) as messages FROM messages GROUP BY name ORDER BY messages DESC;

SELECT '' as '';
SELECT '=== REACCIONES POR EMOJI ===' as '';
SELECT emoji, COUNT(*) as count FROM reactions GROUP BY emoji ORDER BY count DESC;

SELECT '' as '';
SELECT '=== MENSAJES MÁS REACCIONADOS ===' as '';
SELECT m.id, m.name, substr(m.content, 1, 50) as content, COUNT(r.id) as reactions
FROM messages m
LEFT JOIN reactions r ON m.id = r.message_id
GROUP BY m.id
ORDER BY reactions DESC
LIMIT 5;
EOF

# Ejecutar
sqlite3 database/database.sqlite < /tmp/show_all.sql
```

---

## 🔥 COMANDOS RÁPIDOS FAVORITOS

```bash
# Alias útiles (añade a tu .zshrc o .bashrc)
alias chat-messages="cd /Users/glifaus/Code/Sites/laravel-mcp-chat && sqlite3 database/database.sqlite -column -header 'SELECT * FROM messages ORDER BY created_at DESC LIMIT 10;'"

alias chat-stats="cd /Users/glifaus/Code/Sites/laravel-mcp-chat && sqlite3 database/database.sqlite -column -header 'SELECT
(SELECT COUNT(*) FROM messages) as messages,
(SELECT COUNT(DISTINCT name) FROM messages) as users,
(SELECT COUNT(*) FROM reactions) as reactions;'"

alias chat-users="cd /Users/glifaus/Code/Sites/laravel-mcp-chat && sqlite3 database/database.sqlite -column -header 'SELECT name, COUNT(*) as msgs FROM messages GROUP BY name ORDER BY msgs DESC;'"
```

---

## 📊 FORMATO DE SALIDA ESPERADO

### get-messages

```
Here are the latest messages from the "Nuno Nation Chat" server:

- **Patricia**: Docker para dev: PHP-FPM + Nginx + PostgreSQL...
- **Andrés**: Comparando ecosistemas: Composer vs pip, PHPStan...
- **Beatriz**: Proyecto fullstack: Laravel backend + Python...
- **Fernando**: asyncio en Python es brutal para I/O bound tasks...
[... más mensajes ...]
```

### get-users-list

```
Users in Laravelchat (18 users):

- **Carlos** - 1 message (last active 5 minutes ago)
- **María** - 1 message (last active 5 minutes ago)
- **Jorge** - 1 message (last active 5 minutes ago)
[... más usuarios ...]

Total messages: 18
```

### get-message-reactions

```
Reactions for message #1:

Message: "¡Bienvenidos a Knowmadmood! 🚀 Una comunidad..." by Carlos

Total reactions: 4

- 🎉 (2): María, Jorge
- 👍 (1): Ana
- 🚀 (1): Laura
```

---

## ✨ TIPS PARA EXPLORAR

1. **Buscar por tema**: Usa `search-messages` con palabras clave como "Laravel", "Python", "remoto"
2. **Ver conversaciones**: Usa `get-messages-by-user` para seguir la participación de alguien
3. **Analizar engagement**: Usa `get-message-reactions` para ver qué mensajes generan más interacción
4. **Filtrar por fecha**: Usa `get-messages-by-date-range` si necesitas mensajes de un periodo específico

---

## 🎉 ¡Disfruta explorando el chat de Knowmadmood!

La base de datos ya está poblada y lista para usar. Puedes acceder vía:

- ✅ MCP Tools (recomendado para interactividad)
- ✅ CLI/SQL (rápido para debugging)
- ✅ Tinker (flexible para queries complejas)
- ✅ Web (si necesitas interfaz visual)

**¿Siguiente paso?** Prueba a añadir más mensajes y reacciones usando el MCP! 🚀
