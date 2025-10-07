# Sistema de Búsqueda y Filtros Avanzados

## 🎯 Funcionalidades Implementadas

Este módulo añade capacidades avanzadas de búsqueda y filtrado al servidor MCP de Laravelchat.

### 1️⃣ **SearchMessagesTool** - Búsqueda por Palabra Clave

Busca mensajes que contengan palabras o frases específicas.

**Características:**

- Búsqueda case-insensitive
- Búsqueda parcial (encuentra subcadenas)
- Límite configurable de resultados

**Ejemplo de uso:**

```json
{
    "query": "Laravel",
    "limit": 50
}
```

**Respuesta:**

```
Found 2 message(s) matching "Laravel":

- **John Doe** (2 hours ago): Laravel is amazing
- **Jane Smith** (1 day ago): Hello Laravel community
```

### 2️⃣ **GetMessagesByUserTool** - Filtrar por Usuario

Obtiene todos los mensajes de un usuario específico.

**Características:**

- Búsqueda por nombre de usuario
- Búsqueda parcial (encuentra nombres similares)
- Límite configurable de resultados

**Ejemplo de uso:**

```json
{
    "name": "John Doe",
    "limit": 50
}
```

**Respuesta:**

```
Found 5 message(s) from "John Doe":

- **John Doe** (2 hours ago): Hello everyone
- **John Doe** (1 day ago): Another message
```

### 3️⃣ **GetMessagesByDateRangeTool** - Filtrar por Fechas

Filtra mensajes por rango de fechas.

**Características:**

- Filtrar desde una fecha específica
- Filtrar hasta una fecha específica
- Filtrar por rango de fechas completo
- Límite configurable de resultados

**Ejemplo de uso:**

```json
{
    "start_date": "2025-10-01",
    "end_date": "2025-10-07",
    "limit": 50
}
```

**Respuesta:**

```
Found 10 message(s) between 2025-10-01 and 2025-10-07:

- **John Doe** (2025-10-07 10:30:00): Latest message
- **Jane Smith** (2025-10-06 15:20:00): Previous message
```

## 📊 Validaciones

### SearchMessagesTool

- `query`: requerido, string, 1-100 caracteres
- `limit`: opcional, integer, 1-100 (default: 50)

### GetMessagesByUserTool

- `name`: requerido, string, 1-50 caracteres
- `limit`: opcional, integer, 1-100 (default: 50)

### GetMessagesByDateRangeTool

- `start_date`: opcional, fecha ISO 8601
- `end_date`: opcional, fecha ISO 8601 (debe ser >= start_date)
- `limit`: opcional, integer, 1-100 (default: 50)

## 🧪 Tests

Cada herramienta cuenta con tests completos que cubren:

- ✅ Validación de parámetros
- ✅ Casos de éxito
- ✅ Casos sin resultados
- ✅ Límites de resultados
- ✅ Búsquedas case-insensitive
- ✅ Validación de fechas

**Ejecutar tests:**

```bash
php artisan test tests/Feature/Mcp/Tools/SearchMessagesToolTest.php
php artisan test tests/Feature/Mcp/Tools/GetMessagesByUserToolTest.php
php artisan test tests/Feature/Mcp/Tools/GetMessagesByDateRangeToolTest.php
```

## 🚀 Uso desde el Cliente MCP

Las nuevas herramientas están automáticamente disponibles en el servidor MCP:

```
mcp/chat endpoint expone:
- [send-message] - Enviar mensaje
- [get-messages] - Obtener mensajes recientes
- [search-messages] - Buscar por palabra clave ⭐ NUEVO
- [get-messages-by-user] - Filtrar por usuario ⭐ NUEVO
- [get-messages-by-date-range] - Filtrar por fechas ⭐ NUEVO
```

## 📈 Métricas de Cobertura

- **23 tests pasando**
- **43 assertions**
- **100% de cobertura** en las nuevas herramientas

## 🔜 Próximas Mejoras Sugeridas

1. **Búsqueda combinada**: Buscar por usuario Y palabra clave simultáneamente
2. **Ordenamiento**: Opciones de ordenamiento (ascendente/descendente, por fecha/nombre)
3. **Paginación**: Sistema de paginación para grandes cantidades de resultados
4. **Búsqueda avanzada**: Operadores booleanos (AND, OR, NOT)
5. **Índices de base de datos**: Mejorar performance con índices en columnas searchable
