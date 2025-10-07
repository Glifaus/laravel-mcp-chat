# Sistema de Hilos/Threads (Respuestas)

## 🎯 Funcionalidades Implementadas

### ✅ Phase 3: Sistema de Threads completado

- ✅ Migración de base de datos con columna `parent_id`
- ✅ Relaciones en el modelo Message (parent, replies)
- ✅ `ReplyToMessageTool` - Responder a mensajes específicos
- ✅ `GetMessageThreadTool` - Ver hilos de conversación
- ✅ 17 tests pasando (9 + 8)
- ✅ Documentación completa

---

## 🔧 **Cambios en la Base de Datos**

### Migración: `2025_10_07_121429_add_parent_id_to_messages_table`

```php
$table->foreignId('parent_id')
    ->nullable()
    ->after('id')
    ->constrained('messages')
    ->cascadeOnDelete();
```

**Características:**

- `parent_id` nullable (para mensajes principales)
- Foreign key a la misma tabla `messages`
- Cascade delete (si se borra el padre, se borran las respuestas)

---

## 📊 **Modelo Message Actualizado**

### Nuevas Relaciones

```php
/**
 * Get the parent message (if this is a reply).
 */
public function parent(): BelongsTo
{
    return $this->belongsTo(Message::class, 'parent_id');
}

/**
 * Get all replies to this message.
 */
public function replies(): HasMany
{
    return $this->hasMany(Message::class, 'parent_id');
}
```

**Uso:**

```php
// Obtener el mensaje padre
$reply = Message::find(5);
$parent = $reply->parent;

// Obtener todas las respuestas
$message = Message::find(1);
$replies = $message->replies;
$repliesCount = $message->replies()->count();
```

---

## 🛠️ **Herramientas MCP**

### 1️⃣ **ReplyToMessageTool** - Responder a Mensajes

Permite crear respuestas (replies) a mensajes específicos, iniciando hilos de conversación.

**Características:**

- Valida que el mensaje padre existe
- Crea respuesta con `parent_id` establecido
- Muestra preview del mensaje padre (truncado a 50 chars)
- Retorna el ID de la respuesta creada

**Ejemplo de uso:**

```json
{
    "parent_message_id": 1,
    "name": "Jane Smith",
    "content": "Great idea! I totally agree with this approach."
}
```

**Respuesta exitosa:**

```
Reply successfully posted!

**Replying to:** John Doe: "What do you think about implementing threads?..."
**Your reply:** Great idea! I totally agree with this approach.

Reply ID: #5
```

**Validaciones:**

- `parent_message_id`: requerido, integer, debe existir en messages
- `name`: requerido, string, 1-50 caracteres
- `content`: requerido, string, 1-500 caracteres

---

### 2️⃣ **GetMessageThreadTool** - Ver Hilos de Conversación

Muestra un mensaje con todas sus respuestas en formato de hilo.

**Características:**

- Muestra mensaje padre con metadata
- Cuenta total de respuestas
- Lista todas las respuestas cronológicamente
- Incluye reacciones en cada respuesta
- Timestamps relativos (ej: "5 minutes ago")

**Ejemplo de uso:**

```json
{
    "message_id": 1
}
```

**Respuesta exitosa:**

```
Thread for message #1:

**John Doe** (2 hours ago):
What do you think about implementing threads? This would allow better organization of conversations.
📊 3 replies

**Replies:**

↳ **Jane Smith** (1 hour ago):
  Great idea! I totally agree with this approach.
  Reactions: 👍 2 ❤️ 1

↳ **Bob Wilson** (45 minutes ago):
  We should also consider nested replies in the future.

↳ **Alice Brown** (30 minutes ago):
  Let's start simple and iterate from there.
  Reactions: 💯 1
```

**Cuando no hay respuestas:**

```
Thread for message #1:

**John Doe** (2 hours ago):
What do you think about implementing threads?
📊 0 replies

_No replies yet. Be the first to reply!_
```

**Validaciones:**

- `message_id`: requerido, integer, debe existir en messages

---

## 🧪 **Tests**

### ReplyToMessageToolTest (9 tests)

✅ **Funcionalidad básica:**

- `it can reply to a message` - Crea respuesta correctamente
- `it shows reply ID in response` - Muestra el ID de la nueva respuesta
- `it can create multiple replies to same parent` - Múltiples respuestas al mismo mensaje

✅ **Validaciones:**

- `it fails when parent message does not exist` - Error si no existe el padre
- `it validates required fields` - Campos requeridos
- `it validates name length` - Máximo 50 caracteres
- `it validates content length` - Máximo 500 caracteres

✅ **Formato de respuesta:**

- `it truncates long parent message in response` - Trunca a 50 chars + "..."
- `it shows full parent message if short` - Muestra completo si es corto

### GetMessageThreadToolTest (8 tests)

✅ **Funcionalidad básica:**

- `it can get a message thread with replies` - Muestra hilo completo
- `it shows message with no replies` - Maneja mensaje sin respuestas
- `it shows reactions on replies` - Incluye reacciones de las respuestas

✅ **Validaciones:**

- `it fails when message does not exist` - Error si no existe el mensaje
- `it validates required message_id` - Campo message_id requerido

✅ **Formato:**

- `it shows correct reply count for single reply` - Singular "1 reply" (no "1 replies")
- `it shows relative timestamps` - Formato "X ago"
- `it orders replies chronologically` - Orden por created_at

---

## 🚀 **Uso desde el Cliente MCP**

Las herramientas están registradas en el servidor MCP:

```
mcp/chat endpoint incluye:

Herramientas Existentes:
- [send-message] - Enviar mensaje
- [get-messages] - Obtener mensajes recientes
- [search-messages] - Buscar por palabra clave
- [get-messages-by-user] - Filtrar por usuario
- [get-messages-by-date-range] - Filtrar por fechas
- [add-reaction] - Añadir reacción emoji
- [remove-reaction] - Eliminar reacción
- [get-message-reactions] - Ver reacciones de un mensaje
- [get-users-list] - Listar usuarios con estadísticas

Herramientas NUEVAS:
- [reply-to-message] - Responder a un mensaje específico ⭐ NUEVO
- [get-message-thread] - Ver hilo de conversación completo ⭐ NUEVO
```

---

## 💡 **Ejemplos de Uso Completos**

### Flujo de Threads

```javascript
// 1. Enviar mensaje principal
send-message {
  name: "John",
  content: "What's the best way to deploy Laravel apps?"
}
// Response: Message sent, ID: #1

// 2. Responder al mensaje
reply-to-message {
  parent_message_id: 1,
  name: "Alice",
  content: "I recommend using Laravel Forge for easy deployments."
}
// Response: Reply #2 created

// 3. Otra respuesta al mismo mensaje
reply-to-message {
  parent_message_id: 1,
  name: "Bob",
  content: "Docker + AWS ECS is also a great option."
}
// Response: Reply #3 created

// 4. Ver el hilo completo
get-message-thread {
  message_id: 1
}
// Response: Shows parent message + 2 replies

// 5. Añadir reacción a una respuesta
add-reaction {
  message_id: 2,
  user_name: "Charlie",
  emoji: "👍"
}

// 6. Ver el hilo actualizado (con reacciones)
get-message-thread {
  message_id: 1
}
// Response: Shows replies with reactions
```

### Uso típico en conversación

**Usuario:** "Muéstrame el hilo del mensaje #1"

**Asistente usa:** `get-message-thread { message_id: 1 }`

**Usuario:** "Responde a ese mensaje diciendo que estoy de acuerdo"

**Asistente usa:**

```json
reply-to-message {
  parent_message_id: 1,
  name: "[nombre del usuario]",
  content: "Estoy de acuerdo con esta propuesta."
}
```

---

## 📊 **Estadísticas Actualizadas**

### Tests Totales

- **74 tests pasando** (+17 nuevos)
- **196 assertions** (+48 nuevas)
- **0 fallos**
- **Duración:** ~4.5 segundos

### Herramientas MCP

- **11 herramientas activas** (9 anteriores + 2 nuevas)
- **100% de cobertura** en tests

### Base de Datos

- **6 migraciones** (4 originales + 1 reactions + 1 threads)
- **3 tablas principales:** users, messages, reactions
- **2 relaciones auto-referenciadas:** Message → parent, Message → replies

---

## 🎨 **Diseño de Datos**

### Estructura de un Thread

```
Message #1 (parent_id: null) - Mensaje principal
├── Message #2 (parent_id: 1) - Primera respuesta
├── Message #3 (parent_id: 1) - Segunda respuesta
└── Message #4 (parent_id: 1) - Tercera respuesta
```

### Consultas Útiles

```php
// Obtener solo mensajes principales (no respuestas)
$mainMessages = Message::whereNull('parent_id')->get();

// Obtener solo respuestas
$replies = Message::whereNotNull('parent_id')->get();

// Mensajes con más respuestas
$popular = Message::withCount('replies')
    ->orderBy('replies_count', 'desc')
    ->get();

// Respuestas de un mensaje específico
$thread = Message::with('replies.reactions')
    ->find($id);
```

---

## 🔜 **Próximas Mejoras Sugeridas**

### Para Threads:

1. **Respuestas anidadas** - Responder a respuestas (multi-nivel)
2. **Notificaciones** - Notificar al autor cuando alguien responde
3. **Thread subscription** - Seguir hilos de interés
4. **Thread summary** - Resumen de hilos largos
5. **Lock thread** - Cerrar hilos para no permitir más respuestas
6. **Pin replies** - Fijar respuestas importantes en un hilo
7. **Thread tags** - Etiquetar hilos por tema
8. **Thread search** - Buscar dentro de un hilo específico

### Integraciones:

- Combinar con reacciones (ya soportado)
- Combinar con búsquedas (buscar en replies)
- Combinar con usuarios (replies por usuario)

---

## ✅ **Checklist de Implementación**

- [x] Migración de base de datos
- [x] Actualizar modelo Message con relaciones
- [x] Implementar ReplyToMessageTool
- [x] Implementar GetMessageThreadTool
- [x] Registrar herramientas en servidor MCP
- [x] Crear tests para ReplyToMessageTool (9 tests)
- [x] Crear tests para GetMessageThreadTool (8 tests)
- [x] Ejecutar migración en base de datos
- [x] Todos los tests pasando (74 tests)
- [x] Documentación completa

---

## 🎉 **¡Phase 3 Completada!**

El sistema de hilos/threads está 100% funcional y testeado. Los usuarios ahora pueden:

- ✅ Responder a mensajes específicos
- ✅ Ver hilos de conversación completos
- ✅ Ver reacciones en respuestas
- ✅ Navegar por timestamps relativos
- ✅ Identificar fácilmente mensajes principales vs respuestas

**Próximo paso:** Phase 4 - Sistema de Canales/Rooms 🚀
