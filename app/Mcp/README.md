# MCP — Guía de implementación (Tech Day)

Este directorio queda vacío en el starter. Durante el Tech Day implementaremos:

## Objetivos
1) Crear el servidor MCP `Laravelchat` en `app/Mcp/Servers/Laravelchat.php`
2) Publicar 2–3 herramientas básicas:
   - `send-message` (crear mensajes; canal opcional)
   - `get-messages` (listar últimos mensajes)
   - `reply-to-message` (responder a un mensaje y heredar canal)
3) Probar las herramientas con un cliente MCP

## Sugerencias
- Validar inputs (límites: name 1–50, content 1–500, channel <= 50)
- Limitar resultados (1–100, default 50)
- Mantener el código simple y orientado a la demo

## Extensiones (si hay tiempo)
- `search-messages`
- Reacciones (`add/remove/get-message-reactions`)
- Usuarios (`get-users-list`)
