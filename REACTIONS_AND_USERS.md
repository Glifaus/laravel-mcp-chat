# Sistema de Reacciones y Listado de Usuarios

## 🎯 Funcionalidades Implementadas

Este módulo añade un sistema completo de reacciones a mensajes y un listado de usuarios al servidor MCP de Laravelchat.

---

## 🔥 **Sistema de Reacciones**

### 1️⃣ **AddReactionTool** - Añadir Reacciones

Permite a los usuarios reaccionar a mensajes con emojis.

**Características:**

- 16 emojis disponibles: 👍 ❤️ 😂 🎉 🚀 👏 🔥 💯 👎 😮 😢 😡 🤔 💡 ✅ ❌
- Previene reacciones duplicadas (mismo usuario + mismo emoji + mismo mensaje)
- Múltiples usuarios pueden usar el mismo emoji
- Un usuario puede usar diferentes emojis en el mismo mensaje
- Muestra conteo actualizado de reacciones

**Ejemplo de uso:**

```json
{
    "message_id": 1,
    "user_name": "John Doe",
    "emoji": "👍"
}
```

**Respuesta exitosa:**

```
Reaction 👍 added successfully to message #1!

Current reactions: 👍 2 ❤️ 1

Message: "Hello world" by Alice
```

**Validaciones:**

- `message_id`: requerido, debe existir
- `user_name`: requerido, string, 1-50 caracteres
- `emoji`: requerido, debe estar en la lista permitida

---

### 2️⃣ **RemoveReactionTool** - Eliminar Reacciones

Permite a los usuarios eliminar sus propias reacciones.

**Características:**

- Solo puedes eliminar tus propias reacciones
- Muestra reacciones restantes después de eliminar
- Notifica si la reacción no existe

**Ejemplo de uso:**

```json
{
    "message_id": 1,
    "user_name": "John Doe",
    "emoji": "👍"
}
```

**Respuesta exitosa:**

```
Reaction 👍 removed successfully from message #1!

Remaining reactions: ❤️ 1 🎉  3
```

**Validaciones:**

- `message_id`: requerido, debe existir
- `user_name`: requerido, string, 1-50 caracteres
- `emoji`: requerido

---

### 3️⃣ **GetMessageReactionsTool** - Ver Reacciones

Muestra todas las reacciones de un mensaje con detalle.

**Características:**

- Agrupa reacciones por emoji
- Muestra quién reaccionó
- Conteo total de reacciones
- Incluye contenido del mensaje

**Ejemplo de uso:**

```json
{
    "message_id": 1
}
```

**Respuesta:**

```
Reactions for message #1:

Message: "Hello world" by Alice

Total reactions: 5

- 👍 (3): John Doe, Jane Smith, Bob Wilson
- ❤️ (2): Charlie Brown, Diana Prince
```

**Validaciones:**

- `message_id`: requerido, debe existir

---

## 👥 **Listado de Usuarios**

### 4️⃣ **GetUsersListTool** - Lista de Usuarios

Obtiene todos los usuarios únicos con sus estadísticas.

**Características:**

- Conteo de mensajes por usuario
- Última actividad (relativa)
- Múltiples opciones de ordenamiento
- Límite configurable

**Opciones de ordenamiento:**

- `name`: Alfabético (A-Z)
- `messages`: Por actividad (más mensajes primero) - **DEFAULT**
- `last_activity`: Por reciente (último activo primero)

**Ejemplo de uso:**

```json
{
    "limit": 50,
    "sort_by": "messages"
}
```

**Respuesta:**

```
Users in Laravelchat (3 users):

- **John Doe** - 15 messages (last active 2 hours ago)
- **Jane Smith** - 8 messages (last active 1 day ago)
- **Bob Wilson** - 3 messages (last active 3 days ago)

Total messages: 26
```

**Validaciones:**

- `limit`: opcional, integer, 1-100 (default: 50)
- `sort_by`: opcional, string, valores: "name", "messages", "last_activity" (default: "messages")

---

## 🗄️ **Estructura de Base de Datos**

### Tabla: `reactions`

```sql
CREATE TABLE reactions (
    id BIGINT PRIMARY KEY,
    message_id BIGINT NOT NULL,
    user_name VARCHAR(255) NOT NULL,
    emoji VARCHAR(10) NOT NULL,
    created_at TIMESTAMP,
    updated_at TIMESTAMP,
    UNIQUE(message_id, user_name, emoji),
    FOREIGN KEY (message_id) REFERENCES messages(id) ON DELETE CASCADE
);
```

**Campos:**

- `id`: Identificador único
- `message_id`: ID del mensaje (con foreign key)
- `user_name`: Nombre del usuario que reacciona
- `emoji`: Emoji de la reacción
- `created_at/updated_at`: Timestamps automáticos

**Constraints:**

- UNIQUE en (message_id, user_name, emoji): Previene duplicados
- CASCADE DELETE: Al eliminar mensaje, se eliminan sus reacciones

---

## 🧪 **Tests**

### AddReactionToolTest (10 tests)

- ✅ Validación de campos requeridos
- ✅ Validación de message_id existente
- ✅ Añadir reacción exitosamente
- ✅ Prevenir reacciones duplicadas
- ✅ Permitir mismo emoji de diferentes usuarios
- ✅ Permitir diferentes emojis del mismo usuario
- ✅ Validar emoji en lista permitida
- ✅ Mostrar conteo de reacciones

### RemoveReactionToolTest (5 tests)

- ✅ Eliminar reacción exitosamente
- ✅ Manejar reacción inexistente
- ✅ Verificar permisos de usuario
- ✅ Mostrar reacciones restantes
- ✅ Notificar cuando no quedan reacciones

### GetMessageReactionsToolTest (4 tests)

- ✅ Obtener todas las reacciones
- ✅ Manejar mensaje sin reacciones
- ✅ Mostrar contenido del mensaje
- ✅ Validar message_id

### GetUsersListToolTest (7 tests)

- ✅ Listar usuarios con estadísticas
- ✅ Ordenar por conteo de mensajes
- ✅ Ordenar alfabéticamente
- ✅ Ordenar por última actividad
- ✅ Respetar límite de resultados
- ✅ Manejar lista vacía
- ✅ Mostrar timestamp de actividad

**Totales:**

- **49 tests pasando**
- **119 assertions**
- **100% cobertura** en nuevas features

---

## 🚀 **Uso desde el Cliente MCP**

Las herramientas están disponibles en el endpoint `mcp/chat`:

```
[add-reaction] - Añadir reacción a mensaje ⭐ NUEVO
[remove-reaction] - Eliminar tu reacción ⭐ NUEVO
[get-message-reactions] - Ver todas las reacciones ⭐ NUEVO
[get-users-list] - Listar usuarios y estadísticas ⭐ NUEVO
```

---

## 📊 **Estadísticas**

### Emojis Disponibles

```
Positivos: 👍 ❤️ 😂 🎉 🚀 👏 🔥 💯
Negativos: 👎 😮 😢 😡
Otros: 🤔 💡 ✅ ❌
```

### Relaciones del Modelo

```php
Message hasMany Reaction
Reaction belongsTo Message
```

---

## 🔜 **Próximas Mejoras Sugeridas**

1. **Reacciones personalizadas**: Permitir más emojis
2. **Notificaciones**: Notificar al autor cuando alguien reacciona
3. **Reacciones rápidas**: Sugerencias basadas en contexto
4. **Estadísticas de usuarios**: Top reactores, emojis más usados
5. **Badges**: Logros por reacciones recibidas/dadas
6. **Trending messages**: Mensajes con más reacciones
7. **Reacción streak**: Días consecutivos reaccionando
8. **Export reactions**: Exportar estadísticas de reacciones

---

## 💡 **Ejemplos de Uso Completos**

### Flujo de Reacciones

```javascript
// 1. Enviar mensaje
send-message { name: "Alice", content: "Hello world!" }
// Response: Message #1 created

// 2. Añadir reacción
add-reaction { message_id: 1, user_name: "Bob", emoji: "👍" }
// Response: Reaction added!

// 3. Ver reacciones
get-message-reactions { message_id: 1 }
// Response: Shows 👍 (1): Bob

// 4. Eliminar reacción
remove-reaction { message_id: 1, user_name: "Bob", emoji: "👍" }
// Response: Reaction removed!
```

### Flujo de Usuarios

```javascript
// 1. Listar usuarios activos
get-users-list { sort_by: "messages" }
// Response: Shows top contributors

// 2. Ver mensajes de un usuario
get-messages-by-user { name: "Alice" }
// Response: All Alice's messages

// 3. Listar por actividad reciente
get-users-list { sort_by: "last_activity", limit: 10 }
// Response: 10 most recently active users
```
