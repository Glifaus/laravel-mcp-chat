# Sistema de Canales/Rooms - Fase 4

## Resumen

El sistema de canales permite organizar los mensajes en diferentes "salas" o temas, similar a Slack o Discord. Cada mensaje pertenece a un canal específico, lo que facilita la organización de conversaciones por tópicos.

## Características Principales

- **Canal por Defecto**: Todos los mensajes se crean en el canal `general` si no se especifica otro
- **Índice de Base de Datos**: El campo `channel` está indexado para consultas rápidas
- **Herencia de Canal**: Las respuestas (replies) heredan automáticamente el canal del mensaje padre
- **Listado de Canales**: Puedes ver todos los canales activos con estadísticas
- **Filtrado**: Consulta mensajes de un canal específico
- **Validación**: Los canales tienen un límite de 50 caracteres

## Base de Datos

### Migración

```php
Schema::table('messages', function (Blueprint $table) {
    $table->string('channel', 50)
        ->default('general')
        ->after('parent_id')
        ->index();
});
```

### Estructura del Modelo Message

```php
class Message extends Model
{
    protected $fillable = [
        'name',
        'content',
        'channel',  // Nuevo campo
        'parent_id',
    ];

    // ... relaciones ...
}
```

## Herramientas MCP

### 1. GetChannelsTool

Lista todos los canales activos con estadísticas.

**Parámetros:**

- `limit` (opcional): Máximo de canales a retornar (default: 50, max: 100)
- `sort_by` (opcional): Ordenar por "count" (mensajes) o "activity" (último mensaje). Default: "count"

**Ejemplo de Uso:**

```json
{
    "limit": 20,
    "sort_by": "count"
}
```

**Respuesta:**

```
📋 **Active Channels** (3 total)

1️⃣ **#general** - 15 messages
   Last activity: 2 hours ago

2️⃣ **#python** - 8 messages
   Last activity: 1 day ago

3️⃣ **#laravel** - 3 messages
   Last activity: 3 days ago
```

**Características:**

- Cuenta solo mensajes raíz (excluye replies)
- Ordena por cantidad de mensajes (descendente) por defecto
- Muestra timestamps relativos
- Usa singular/plural correctamente ("1 message" vs "2 messages")
- Maneja canales vacíos

### 2. GetChannelMessagesTool

Obtiene mensajes de un canal específico.

**Parámetros:**

- `channel` (requerido): Nombre del canal
- `limit` (opcional): Máximo de mensajes (default: 50, max: 100)

**Ejemplo de Uso:**

```json
{
    "channel": "python",
    "limit": 10
}
```

**Respuesta:**

```
💬 **Messages from #python** (8 messages)

[ID: 42] **Alice** (2 hours ago):
Great discussion about async/await!
👍 2  🚀 1

[ID: 38] **Bob** (1 day ago):
Check out this Python tutorial...
❤️ 3
```

**Características:**

- Filtra por canal específico
- Excluye respuestas (solo mensajes raíz)
- Muestra reacciones de cada mensaje
- Respeta el límite de mensajes
- Valida que el canal sea requerido
- Muestra timestamps relativos

### 3. SendMessageTool (Actualizado)

Envía un mensaje a un canal específico.

**Parámetros:**

- `name` (requerido): Nombre del usuario (1-50 caracteres)
- `content` (requerido): Contenido del mensaje (1-500 caracteres)
- `channel` (opcional): Canal donde enviar el mensaje (default: 'general', max: 50 caracteres)

**Ejemplo de Uso:**

```json
{
    "name": "Alice",
    "content": "Hello from Python channel!",
    "channel": "python"
}
```

**Respuesta:**

```
✅ Message sent to **#python**!

[ID: 123] Your message was delivered successfully.
```

**Cambios:**

- Añadido parámetro opcional `channel`
- Validación de longitud del canal (max 50 caracteres)
- Respuesta incluye el nombre del canal
- Default a 'general' si no se especifica

### 4. ReplyToMessageTool (Actualizado)

Responde a un mensaje, heredando automáticamente su canal.

**Parámetros:**

- `message_id` (requerido): ID del mensaje padre
- `name` (requerido): Nombre del usuario
- `content` (requerido): Contenido de la respuesta

**Ejemplo de Uso:**

```json
{
    "message_id": 42,
    "name": "Bob",
    "content": "I agree with your point!"
}
```

**Respuesta:**

```
✅ Reply successfully posted in **#python**!

[ID: 124] Replying to message #42:
"Great discussion about async/await!"

Your reply: "I agree with your point!"

💡 Use get-message-thread to see the full conversation.
```

**Cambios:**

- Hereda automáticamente el canal del mensaje padre
- Respuesta incluye el nombre del canal
- No permite especificar canal manualmente (siempre hereda)

## Integración con Otras Características

### Hilos (Threads)

Las respuestas heredan el canal del mensaje padre:

```php
// Mensaje padre en #python
$parent = Message::create([
    'name' => 'Alice',
    'content' => 'Python question...',
    'channel' => 'python',
]);

// Respuesta automáticamente en #python
$reply = Message::create([
    'name' => 'Bob',
    'content' => 'Here is the answer...',
    'channel' => $parent->channel,  // Hereda 'python'
    'parent_id' => $parent->id,
]);
```

### Reacciones

Las reacciones funcionan normalmente en mensajes de cualquier canal. No hay restricciones por canal.

### Búsqueda

Las herramientas de búsqueda (`SearchMessagesTool`, `GetMessagesByUserTool`, `GetMessagesByDateRangeTool`) buscan en **todos los canales**. Para buscar en un canal específico, usa `GetChannelMessagesTool`.

## Casos de Uso

### 1. Organización por Temas

```bash
# Canal para PHP/Laravel
canal: laravel
- Discusiones sobre Eloquent
- Preguntas sobre Blade
- Tips de Laravel

# Canal para Python
canal: python
- Tutoriales de Python
- Async/await discussions
- Framework comparisons
```

### 2. Proyectos o Equipos

```bash
# Canal del proyecto Alpha
canal: project-alpha
- Updates del proyecto
- Bug reports
- Feature requests

# Canal del equipo de desarrollo
canal: dev-team
- Standup notes
- Code reviews
- Technical discussions
```

### 3. Eventos o Temporales

```bash
# Canal para un evento
canal: hackathon-2025
- Anuncios del evento
- Entregas de participantes
- Votaciones

# Canal temporal
canal: q1-planning
- Planning de trimestre
- OKRs
- Retrospectivas
```

## Ejemplos de Flujos

### Crear y Usar un Nuevo Canal

1. **Enviar primer mensaje al canal:**

    ```json
    {
        "name": "Alice",
        "content": "Starting a new discussion about TypeScript!",
        "channel": "typescript"
    }
    ```

2. **Listar canales para verificar:**
    ```json
    {
        "limit": 10
    }
    ```
3. **Consultar mensajes del nuevo canal:**
    ```json
    {
        "channel": "typescript",
        "limit": 50
    }
    ```

### Conversación en Canal Específico

1. **Alice envía mensaje en #python:**

    ```json
    {
        "name": "Alice",
        "content": "What's the best way to handle async operations?",
        "channel": "python"
    }
    ```

2. **Bob responde (automáticamente en #python):**

    ```json
    {
        "message_id": 123,
        "name": "Bob",
        "content": "I recommend using asyncio with async/await"
    }
    ```

3. **Ver hilo completo (incluye canal):**
    ```json
    {
        "message_id": 123
    }
    ```

## Tests

### GetChannelsToolTest (6 tests, 14 assertions)

```php
✓ it lists all channels with message counts
✓ it orders channels by message count
✓ it shows last activity for each channel
✓ it handles empty channels list
✓ it shows channel count
✓ it uses plural correctly for single message
```

### GetChannelMessagesToolTest (8 tests, 25 assertions)

```php
✓ it gets messages from a specific channel
✓ it does not show messages from other channels
✓ it excludes reply messages from channel view
✓ it validates channel is required
✓ it handles channel with no messages
✓ it respects limit parameter
✓ it defaults to 50 messages limit
✓ it shows relative timestamps
```

### Tests Actualizados

Se actualizaron los siguientes tests para soportar el nuevo campo `channel`:

- `MessageTest`: Incluye 'channel' en las claves esperadas del array
- `SendMessageToolTest`: Verifica mensaje "sent to **#general**"
- `ReplyToMessageToolTest`: Verifica "Reply successfully posted" con mención del canal

## Rendimiento

### Índices de Base de Datos

El campo `channel` está indexado para optimizar consultas:

```sql
CREATE INDEX messages_channel_index ON messages(channel);
```

**Beneficios:**

- Consultas rápidas por canal (`WHERE channel = 'python'`)
- Agrupaciones eficientes (`GROUP BY channel`)
- Ordenamiento optimizado en listados

### Consultas Optimizadas

```php
// Listado de canales - optimizado con GROUP BY indexado
Message::query()
    ->select('channel')
    ->selectRaw('COUNT(*) as count')
    ->selectRaw('MAX(created_at) as last_activity')
    ->whereNull('parent_id')
    ->groupBy('channel')
    ->orderByDesc('count')
    ->limit($limit)
    ->get();

// Mensajes de un canal - optimizado con índice
Message::query()
    ->where('channel', $channel)  // Usa el índice
    ->whereNull('parent_id')
    ->orderByDesc('created_at')
    ->limit($limit)
    ->get();
```

## Limitaciones Conocidas

1. **Sin Gestión de Canales**: No hay herramientas para crear, renombrar o eliminar canales explícitamente. Los canales se crean automáticamente al enviar el primer mensaje.

2. **Sin Permisos**: No hay sistema de permisos. Cualquier usuario puede leer y escribir en cualquier canal.

3. **Sin Canales Privados**: Todos los canales son públicos. No hay soporte para canales privados o directos.

4. **Sin Descripción de Canales**: Los canales no tienen descripción, tema o metadata adicional.

5. **Sin Notificaciones**: No hay sistema de notificaciones cuando se envía un mensaje a un canal.

## Mejoras Futuras Sugeridas

### 1. Gestión de Canales

```php
// CreateChannelTool
$channel = Channel::create([
    'name' => 'python',
    'description' => 'All things Python',
    'created_by' => 'Alice',
]);

// UpdateChannelTool
$channel->update([
    'description' => 'Python programming discussions',
    'topic' => 'Currently discussing: asyncio',
]);

// DeleteChannelTool (soft delete)
$channel->delete();
```

### 2. Metadata de Canales

```php
Schema::create('channels', function (Blueprint $table) {
    $table->id();
    $table->string('name', 50)->unique();
    $table->string('description')->nullable();
    $table->string('topic')->nullable();
    $table->boolean('is_private')->default(false);
    $table->string('created_by', 50);
    $table->timestamps();
    $table->softDeletes();
});
```

### 3. Miembros de Canal

```php
Schema::create('channel_members', function (Blueprint $table) {
    $table->id();
    $table->foreignId('channel_id')->constrained();
    $table->string('user_name', 50);
    $table->enum('role', ['owner', 'admin', 'member']);
    $table->timestamp('joined_at');
    $table->timestamp('last_read_at')->nullable();
});
```

### 4. Canales Directos (DMs)

```php
// Crear canal privado entre dos usuarios
$dm = Channel::createDirect('Alice', 'Bob');
// Resultado: canal 'dm-alice-bob' privado
```

### 5. Búsqueda en Canal Específico

```php
// Extender SearchMessagesTool
SearchMessagesTool::invoke([
    'query' => 'async',
    'channel' => 'python',  // Nuevo parámetro opcional
]);
```

### 6. Subscripciones y Notificaciones

```php
// SubscribeToChannelTool
ChannelSubscription::create([
    'channel' => 'python',
    'user_name' => 'Alice',
    'notify_on' => ['new_message', 'mention'],
]);
```

## Arquitectura

### Flujo de Creación de Mensaje con Canal

```
SendMessageTool
    ↓
CreateMessageAction::handle($name, $content, $channel = 'general')
    ↓
Message::create([
    'name' => $name,
    'content' => $content,
    'channel' => $channel,
])
    ↓
Respuesta con canal incluido
```

### Flujo de Respuesta con Herencia de Canal

```
ReplyToMessageTool
    ↓
$parent = Message::findOrFail($messageId)
    ↓
CreateMessageAction::handle($name, $content, $parent->channel)
    ↓
Message::create([
    'name' => $name,
    'content' => $content,
    'channel' => $parent->channel,  // Heredado
    'parent_id' => $parent->id,
])
    ↓
Respuesta con canal heredado
```

### Diagrama de Relaciones

```
Message (tabla principal)
├── channel (string, indexed) ─────┐
├── parent_id (nullable) ──────────┼── Organización
├── name (string)                  │
├── content (text)                 │
└── created_at (timestamp)         │
                                   │
Índices:                           │
- messages_channel_index ──────────┘
- messages_parent_id_foreign

Consultas Optimizadas:
1. WHERE channel = 'X' → Usa índice
2. GROUP BY channel → Usa índice
3. WHERE channel = 'X' AND parent_id IS NULL → Compuesto eficiente
```

## Conclusión

El sistema de canales de la Fase 4 proporciona una forma simple pero efectiva de organizar conversaciones por tópicos. La implementación es minimalista pero extensible, con un diseño que facilita agregar características más avanzadas en el futuro.

**Estadísticas Finales:**

- ✅ 2 nuevas herramientas MCP
- ✅ 2 herramientas actualizadas
- ✅ 14 nuevos tests (6 + 8)
- ✅ 39 aserciones totales
- ✅ 1 migración de base de datos
- ✅ Índice para optimización
- ✅ 88 tests totales pasando
- ✅ 235 aserciones totales

**Compatibilidad:**

- ✅ Compatible con sistema de hilos (Fase 3)
- ✅ Compatible con reacciones (Fase 2)
- ✅ Compatible con búsqueda y filtros (Fase 1)
- ✅ Sin cambios breaking en API existente
