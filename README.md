# Laravel MCP Chat

<a href="https://github.com/codespaces/new?hide_repo_select=true&amp;ref=techday-starter&amp;repo=Glifaus%2Flaravel-mcp-chat"><img src="https://github.com/codespaces/badge.svg"></a>

## ⚡ Tech Day (18 de noviembre)
- Guía del workshop: <a>WORKSHOP.md</a>
- Arranque rápido en Codespaces: usa el badge de arriba (abre un Codespace en la rama techday-starter)

## Estado de esta rama: techday-starter
Esta rama es un starter didáctico para el Tech Day. Incluye modelos, migraciones y semilla opcional, pero aún NO contiene el servidor MCP ni las herramientas MCP. Las implementaremos paso a paso durante la sesión.

## Checklist del Workshop
- [ ] Abrir el repositorio en GitHub Codespaces (o preparar entorno local)
- [ ] Verificar dependencias instaladas automáticamente (Composer/npm) y migraciones
- [ ] Crear BD SQLite si no existe (`database/database.sqlite`) y ejecutar migraciones
- [ ] (Opcional) Sembrar datos: `php artisan db:seed --class=Database\\Seeders\\KnowmadmoodSeeder`
- [ ] Iniciar el servidor: `php artisan serve --host=0.0.0.0 --port=8000`
- [ ] Crear el servidor MCP en `app/Mcp/Servers/Laravelchat.php`
- [ ] Implementar herramientas básicas: `send-message`, `get-messages`, `reply-to-message`
- [ ] Probar `/mcp/chat` con payloads de ejemplo (cliente MCP)
- [ ] Integrar un agente mínimo con Laravel Boost que consuma esas herramientas
- [ ] Ejercicio: responder a un mensaje y listar el hilo
- [ ] Q&amp;A y siguientes pasos

Aplicación de chat minimalista construida con Laravel 12 (modelos y migraciones listas) y preparada para integrar un servidor MCP (Model Context Protocol) durante el Tech Day.

Requisitos: PHP 8.4+, SQLite, Node.js (para assets), Composer y npm.

Nota: La rama `main` contiene la versión completa con servidor y herramientas MCP ya implementadas.

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
- Listado de canales: GROUP BY channel con MAX(created_at) y COUNT(*)

## 🛠️ Puesta en marcha (starter)

1) Dependencias y entorno
- Copia .env y genera key
- Asegúrate de tener SQLite disponible

2) Instalación y build
- `composer install`
- `npm install`
- `npm run build` (o `npm run dev`)

3) Base de datos
- Crear archivo SQLite: `database/database.sqlite` (si no existe)
- `php artisan migrate`
- Opcional: poblar dataset de ejemplo Knowmadmood
  - `php artisan db:seed --class=Database\\Seeders\\KnowmadmoodSeeder`

4) Servidor
- `php artisan serve`

## 🎯 Objetivos MCP del taller (se implementan en vivo)
- Crear servidor MCP "Laravelchat" y registrar:
  - `send-message` (crear mensajes; canal opcional)
  - `get-messages` (listar últimos mensajes)
  - `reply-to-message` (responder y crear hilos)
- Probar las herramientas con un cliente MCP
- Mostrar un agente de Laravel Boost consumiendo estas herramientas

Para consultar la implementación completa, revisa la rama `main`.
