<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Message;
use App\Models\Reaction;
use Illuminate\Database\Seeder;

final class KnowmadmoodSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Limpiar datos existentes
        Reaction::query()->delete();
        Message::query()->delete();

        $this->command->info('🧹 Base de datos limpiada');

        // Knowmadmood Community
        $messages = [
            [
                'name' => 'Carlos',
                'content' => '¡Bienvenidos a Knowmadmood! 🚀 Una comunidad donde los nómadas digitales y profesionales remotos compartimos experiencias, conocimientos y oportunidades.',
            ],
            [
                'name' => 'María',
                'content' => 'Llevo 3 años trabajando remotamente desde diferentes países. ¿Alguien tiene experiencia trabajando desde Asia? Me encantaría conocer sus tips sobre visas y coworking spaces.',
            ],
            [
                'name' => 'Jorge',
                'content' => 'Organizamos meetups virtuales cada miércoles a las 18:00 CET. El próximo tema: \'Herramientas de productividad para equipos distribuidos\'. ¡Apúntate!',
            ],
            [
                'name' => 'Ana',
                'content' => 'He creado una lista de recursos útiles para nómadas: mejores seguros de viaje, bancos digitales, apps de productividad. ¿Qué más debería incluir?',
            ],
            [
                'name' => 'David',
                'content' => 'Buscamos un desarrollador Full Stack para proyecto remoto. Stack: Laravel, Vue.js, PostgreSQL. Contrato 6 meses renovable. Interesados escribir por DM.',
            ],

            // PHP Expert Center
            [
                'name' => 'Laura',
                'content' => 'Centro Experto PHP: Bienvenidos al hub de desarrollo PHP! Laravel, Symfony, WordPress... compartimos código, resolvemos dudas y mejoramos juntos 🐘',
            ],
            [
                'name' => 'Roberto',
                'content' => '¿Cuáles son sus best practices para estructurar proyectos Laravel grandes? Yo uso Actions, DTOs y separación por dominio. ¿Qué opinan de arquitectura hexagonal?',
            ],
            [
                'name' => 'Sofia',
                'content' => 'PHP 8.4 trae property hooks y lazy objects! 🎉 Alguien ya los probó? Las property hooks parecen muy útiles para eliminar getters/setters boilerplate.',
            ],
            [
                'name' => 'Miguel',
                'content' => 'Pest vs PHPUnit: debate eterno. Mi voto para Pest, la sintaxis es mucho más limpia. ¿Qué prefieren ustedes? Compartan sus experiencias con testing en PHP.',
            ],
            [
                'name' => 'Elena',
                'content' => 'Tips de performance en Laravel: usar eager loading, cachear queries, optimizar N+1 queries, Redis para sesiones. ¿Qué otras técnicas usan para optimizar?',
            ],

            // Python Expert Center
            [
                'name' => 'Pedro',
                'content' => 'Centro Experto Python: El lugar donde los pythonistas comparten conocimiento! Django, FastAPI, Data Science, ML... todos bienvenidos 🐍✨',
            ],
            [
                'name' => 'Carmen',
                'content' => 'Estoy evaluando FastAPI vs Django REST Framework para una nueva API. FastAPI es más rápido pero DRF tiene más baterías incluidas. ¿Experiencias?',
            ],
            [
                'name' => 'Alberto',
                'content' => 'Proyecto de análisis de datos: pandas + numpy + matplotlib = combo perfecto. ¿Alguien usa Polars? He leído que es mucho más rápido que pandas.',
            ],
            [
                'name' => 'Isabel',
                'content' => 'Implementando modelo de ML con scikit-learn para clasificación. Accuracy del 94%! Próximo paso: probar con TensorFlow para deep learning. Tips?',
            ],
            [
                'name' => 'Fernando',
                'content' => 'asyncio en Python es brutal para I/O bound tasks. Convertí mi scraper y pasó de 10 min a 30 seg. ¿Casos de uso donde async hizo gran diferencia?',
            ],

            // Mensajes Cruzados
            [
                'name' => 'Beatriz',
                'content' => 'Proyecto fullstack: Laravel backend + Python microservicio para ML. Comunicación via API REST. ¿Mejores prácticas para integrar PHP y Python?',
            ],
            [
                'name' => 'Andrés',
                'content' => 'Comparando ecosistemas: Composer vs pip, PHPStan vs mypy, Pest vs pytest. Ambos lenguajes tienen excelentes herramientas de calidad!',
            ],
            [
                'name' => 'Patricia',
                'content' => 'Docker para dev: PHP-FPM + Nginx + PostgreSQL en un container, Python con uvicorn en otro. ¿Usan docker-compose o Kubernetes para desarrollo?',
            ],
        ];

        foreach ($messages as $messageData) {
            Message::query()->create($messageData);
        }

        $this->command->info('✅ 18 mensajes creados');

        // Añadir reacciones variadas
        $messageIds = Message::query()->pluck('id')->toArray();

        // Reacciones al mensaje de bienvenida (id: 1)
        if (isset($messageIds[0])) {
            Reaction::query()->create(['message_id' => $messageIds[0], 'user_name' => 'María', 'emoji' => '🎉']);
            Reaction::query()->create(['message_id' => $messageIds[0], 'user_name' => 'Jorge', 'emoji' => '🎉']);
            Reaction::query()->create(['message_id' => $messageIds[0], 'user_name' => 'Ana', 'emoji' => '👍']);
            Reaction::query()->create(['message_id' => $messageIds[0], 'user_name' => 'Laura', 'emoji' => '🚀']);
        }

        // Reacciones al mensaje de trabajo remoto (id: 2)
        if (isset($messageIds[1])) {
            Reaction::query()->create(['message_id' => $messageIds[1], 'user_name' => 'Carlos', 'emoji' => '🤔']);
            Reaction::query()->create(['message_id' => $messageIds[1], 'user_name' => 'David', 'emoji' => '👍']);
        }

        // Reacciones al mensaje de Laravel (id: 7)
        if (isset($messageIds[6])) {
            Reaction::query()->create(['message_id' => $messageIds[6], 'user_name' => 'Carlos', 'emoji' => '❤️']);
            Reaction::query()->create(['message_id' => $messageIds[6], 'user_name' => 'Sofia', 'emoji' => '🔥']);
            Reaction::query()->create(['message_id' => $messageIds[6], 'user_name' => 'Miguel', 'emoji' => '👍']);
        }

        // Reacciones al mensaje de PHP 8.4 (id: 8)
        if (isset($messageIds[7])) {
            Reaction::query()->create(['message_id' => $messageIds[7], 'user_name' => 'Roberto', 'emoji' => '🎉']);
            Reaction::query()->create(['message_id' => $messageIds[7], 'user_name' => 'Elena', 'emoji' => '🚀']);
            Reaction::query()->create(['message_id' => $messageIds[7], 'user_name' => 'Laura', 'emoji' => '💯']);
        }

        // Reacciones al mensaje de Pest vs PHPUnit (id: 9)
        if (isset($messageIds[8])) {
            Reaction::query()->create(['message_id' => $messageIds[8], 'user_name' => 'Laura', 'emoji' => '👍']);
            Reaction::query()->create(['message_id' => $messageIds[8], 'user_name' => 'Sofia', 'emoji' => '👍']);
        }

        // Reacciones al mensaje de Python (id: 11)
        if (isset($messageIds[10])) {
            Reaction::query()->create(['message_id' => $messageIds[10], 'user_name' => 'Carmen', 'emoji' => '🐍']);
            Reaction::query()->create(['message_id' => $messageIds[10], 'user_name' => 'Alberto', 'emoji' => '🎉']);
            Reaction::query()->create(['message_id' => $messageIds[10], 'user_name' => 'Isabel', 'emoji' => '👏']);
        }

        // Reacciones al mensaje de ML (id: 14)
        if (isset($messageIds[13])) {
            Reaction::query()->create(['message_id' => $messageIds[13], 'user_name' => 'Pedro', 'emoji' => '💯']);
            Reaction::query()->create(['message_id' => $messageIds[13], 'user_name' => 'Fernando', 'emoji' => '🚀']);
            Reaction::query()->create(['message_id' => $messageIds[13], 'user_name' => 'Carmen', 'emoji' => '🔥']);
        }

        // Reacciones al mensaje de async Python (id: 15)
        if (isset($messageIds[14])) {
            Reaction::query()->create(['message_id' => $messageIds[14], 'user_name' => 'Alberto', 'emoji' => '🔥']);
            Reaction::query()->create(['message_id' => $messageIds[14], 'user_name' => 'Pedro', 'emoji' => '💡']);
            Reaction::query()->create(['message_id' => $messageIds[14], 'user_name' => 'Isabel', 'emoji' => '👍']);
        }

        // Reacciones al mensaje de integración PHP+Python (id: 16)
        if (isset($messageIds[15])) {
            Reaction::query()->create(['message_id' => $messageIds[15], 'user_name' => 'Roberto', 'emoji' => '💡']);
            Reaction::query()->create(['message_id' => $messageIds[15], 'user_name' => 'Carmen', 'emoji' => '🤔']);
            Reaction::query()->create(['message_id' => $messageIds[15], 'user_name' => 'Laura', 'emoji' => '👍']);
        }

        // Reacciones al mensaje de Docker (id: 18)
        if (isset($messageIds[17])) {
            Reaction::query()->create(['message_id' => $messageIds[17], 'user_name' => 'Carlos', 'emoji' => '🚀']);
            Reaction::query()->create(['message_id' => $messageIds[17], 'user_name' => 'David', 'emoji' => '👏']);
        }

        $this->command->info('✅ ' . Reaction::count() . ' reacciones añadidas');
        $this->command->info('');
        $this->command->info('🎉 ¡Base de datos poblada exitosamente!');
        $this->command->info('');
        $this->command->info('📊 Estadísticas:');
        $this->command->info('   - Mensajes: ' . Message::count());
        $this->command->info('   - Reacciones: ' . Reaction::count());
        $this->command->info('   - Usuarios únicos: ' . Message::query()->distinct('name')->count('name'));
    }
}
