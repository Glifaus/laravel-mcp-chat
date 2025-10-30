# Tech Day: Laravel + MCP + Boost (Guía para asistentes)

## Objetivo
Levantar una app de chat con Laravel 12 que expone un servidor MCP y publicar 2–3 herramientas básicas (send-message, get-messages, reply-to-message). Ver un agente con Laravel Boost consumiéndolas.

## Prework (si NO usas Codespaces)
- PHP 8.4+ y Composer
- Node 18+ (LTS) y npm
- SQLite y Git
- Editor (VS Code recomendado)

## Inicio rápido con GitHub Codespaces (recomendado)
1. Haz fork del repositorio.
2. Abre el fork en Codespaces (Code → Create codespace on main).
3. Espera la preparación automática (instalación de dependencias y migraciones).
4. Inicia el servidor:
   ```bash
   php artisan serve --host=0.0.0.0 --port=8000
   ```
5. Abre la URL reenviada del puerto 8000.
6. (Opcional) Carga datos de ejemplo:
   ```bash
   php artisan db:seed --class=Database\\Seeders\\KnowmadmoodSeeder
   ```

## Inicio rápido en local
```bash
cp .env.example .env
php artisan key:generate
composer install
npm install && npm run build
touch database/database.sqlite
php artisan migrate
# Opcional: datos de ejemplo
php artisan db:seed --class=Database\\Seeders\\KnowmadmoodSeeder
php artisan serve
```

## Endpoint MCP
- URL local: `/mcp/chat`

## Herramientas MCP (payloads ejemplo)
- send-message
  ```json
  { "name": "Alice", "content": "Hola", "channel": "general" }
  ```
- get-messages
  ```json
  { "limit": 10 }
  ```
- reply-to-message
  ```json
  { "parent_message_id": 1, "name": "Bob", "content": "Totalmente de acuerdo" }
  ```

## Agenda (3 horas)
- 0:00–0:10 Intro MCP + objetivo
- 0:10–0:35 Setup (Codespaces/local) y estructura
- 0:35–1:10 Dominio del chat (migraciones/seed/modelos)
- 1:10–1:45 Herramientas MCP: send/get/reply + prueba
- 1:45–2:20 Laravel Boost: agente mínimo
- 2:20–2:50 Ejercicio guiado (hilo y/o búsqueda rápida)
- 2:50–3:00 Q&A y próximos pasos

## Retos sugeridos
- Responder al mensaje #1 y ver el hilo completo
- Enviar mensaje a un canal propio (php/python/devops)
- Buscar mensajes que contengan "Laravel"

## Problemas frecuentes
- No ves cambios de frontend: `npm run dev` o `npm run build`
- BD vacía: `php artisan migrate` y seeder opcional
- Puerto 8000: ya en uso → usa `--port=8001`

## Recursos
- Repo: https://github.com/Glifaus/laravel-mcp-chat
- Endpoint de referencia: http://localhost:8000/mcp/chat
