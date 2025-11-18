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

        // Canal: general - Bienvenida y comunidad
        $msg1 = Message::query()->create([
            'name' => 'Carlos',
            'content' => '¡Bienvenidos a Knowmadmood! 🚀 Una comunidad donde los nómadas digitales y profesionales remotos compartimos experiencias, conocimientos y oportunidades.',
            'channel' => 'general',
            'created_at' => now()->subDays(7),
        ]);

        $msg2 = Message::query()->create([
            'name' => 'María',
            'content' => 'Llevo 3 años trabajando remotamente desde diferentes países. ¿Alguien tiene experiencia trabajando desde Asia? Me encantaría conocer sus tips sobre visas y coworking spaces.',
            'channel' => 'general',
            'created_at' => now()->subDays(6),
        ]);

        // Respuesta al mensaje de María (hilo)
        Message::query()->create([
            'name' => 'Jorge',
            'content' => 'Yo viví en Tailandia 6 meses. La visa de turista renovable es fácil y Bangkok tiene coworkings increíbles como Hubba-To. Chiang Mai es más barato.',
            'channel' => 'general',
            'parent_id' => $msg2->id,
            'created_at' => now()->subDays(6)->addHours(2),
        ]);

        Message::query()->create([
            'name' => 'Ana',
            'content' => 'Vietnam también es genial! Ho Chi Minh tiene una comunidad de nómadas muy activa. Recomiendo el grupo de Facebook "Saigon Digital Nomads".',
            'channel' => 'general',
            'parent_id' => $msg2->id,
            'created_at' => now()->subDays(6)->addHours(5),
        ]);

        Message::query()->create([
            'name' => 'Jorge',
            'content' => 'Organizamos meetups virtuales cada miércoles a las 18:00 CET. El próximo tema: \'Herramientas de productividad para equipos distribuidos\'. ¡Apúntate!',
            'channel' => 'general',
            'created_at' => now()->subDays(5),
        ]);

        $msg6 = Message::query()->create([
            'name' => 'Ana',
            'content' => 'He creado una lista de recursos útiles para nómadas: mejores seguros de viaje, bancos digitales, apps de productividad. ¿Qué más debería incluir?',
            'channel' => 'general',
            'created_at' => now()->subDays(4),
        ]);

        // Hilo con múltiples respuestas
        Message::query()->create([
            'name' => 'Carlos',
            'content' => 'Añade SIM cards internacionales como Airalo! Me salvó en muchos países.',
            'channel' => 'general',
            'parent_id' => $msg6->id,
            'created_at' => now()->subDays(4)->addHours(1),
        ]);

        Message::query()->create([
            'name' => 'David',
            'content' => 'Y VPNs! NordVPN o ExpressVPN son esenciales para seguridad en WiFis públicos.',
            'channel' => 'general',
            'parent_id' => $msg6->id,
            'created_at' => now()->subDays(4)->addHours(3),
        ]);

        // Canal: jobs - Ofertas de trabajo
        Message::query()->create([
            'name' => 'David',
            'content' => 'Buscamos un desarrollador Full Stack para proyecto remoto. Stack: Laravel, Vue.js, PostgreSQL. Contrato 6 meses renovable. Interesados escribir por DM.',
            'channel' => 'jobs',
            'created_at' => now()->subDays(3),
        ]);

        Message::query()->create([
            'name' => 'Patricia',
            'content' => 'Startup busca Python Developer especializado en ML. Trabajo 100% remoto, equipo internacional. Stack: FastAPI, TensorFlow, Docker. Salario competitivo.',
            'channel' => 'jobs',
            'created_at' => now()->subDays(2),
        ]);

        // Canal: php - PHP Expert Center
        $msg11 = Message::query()->create([
            'name' => 'Laura',
            'content' => 'Centro Experto PHP: Bienvenidos al hub de desarrollo PHP! Laravel, Symfony, WordPress... compartimos código, resolvemos dudas y mejoramos juntos 🐘',
            'channel' => 'php',
            'created_at' => now()->subDays(6),
        ]);

        $msg12 = Message::query()->create([
            'name' => 'Roberto',
            'content' => '¿Cuáles son sus best practices para estructurar proyectos Laravel grandes? Yo uso Actions, DTOs y separación por dominio. ¿Qué opinan de arquitectura hexagonal?',
            'channel' => 'php',
            'created_at' => now()->subDays(5),
        ]);

        // Hilo sobre arquitectura Laravel
        Message::query()->create([
            'name' => 'Laura',
            'content' => 'Hexagonal es genial para proyectos complejos. Separa lógica de negocio del framework. Pero para apps medianas, Actions + Service Classes funcionan perfecto.',
            'channel' => 'php',
            'parent_id' => $msg12->id,
            'created_at' => now()->subDays(5)->addHours(2),
        ]);

        Message::query()->create([
            'name' => 'Miguel',
            'content' => 'Yo prefiero DDD (Domain-Driven Design) con Laravel. Agregados, Value Objects, Events... mantiene el código muy limpio.',
            'channel' => 'php',
            'parent_id' => $msg12->id,
            'created_at' => now()->subDays(5)->addHours(4),
        ]);

        Message::query()->create([
            'name' => 'Sofia',
            'content' => 'PHP 8.4 trae property hooks y lazy objects! 🎉 Alguien ya los probó? Las property hooks parecen muy útiles para eliminar getters/setters boilerplate.',
            'channel' => 'php',
            'created_at' => now()->subDays(4),
        ]);

        $msg16 = Message::query()->create([
            'name' => 'Miguel',
            'content' => 'Pest vs PHPUnit: debate eterno. Mi voto para Pest, la sintaxis es mucho más limpia. ¿Qué prefieren ustedes? Compartan sus experiencias con testing en PHP.',
            'channel' => 'php',
            'created_at' => now()->subDays(3),
        ]);

        // Respuestas sobre testing
        Message::query()->create([
            'name' => 'Roberto',
            'content' => 'Pest 100%! Los tests se leen como especificaciones. Y los datasets son una maravilla para testing parametrizado.',
            'channel' => 'php',
            'parent_id' => $msg16->id,
            'created_at' => now()->subDays(3)->addHours(1),
        ]);

        Message::query()->create([
            'name' => 'Elena',
            'content' => 'Tips de performance en Laravel: usar eager loading, cachear queries, optimizar N+1 queries, Redis para sesiones. ¿Qué otras técnicas usan para optimizar?',
            'channel' => 'php',
            'created_at' => now()->subDays(2),
        ]);

        // Canal: python - Python Expert Center
        $msg19 = Message::query()->create([
            'name' => 'Pedro',
            'content' => 'Centro Experto Python: El lugar donde los pythonistas comparten conocimiento! Django, FastAPI, Data Science, ML... todos bienvenidos 🐍✨',
            'channel' => 'python',
            'created_at' => now()->subDays(6),
        ]);

        $msg20 = Message::query()->create([
            'name' => 'Carmen',
            'content' => 'Estoy evaluando FastAPI vs Django REST Framework para una nueva API. FastAPI es más rápido pero DRF tiene más baterías incluidas. ¿Experiencias?',
            'channel' => 'python',
            'created_at' => now()->subDays(5),
        ]);

        // Hilo sobre frameworks
        Message::query()->create([
            'name' => 'Pedro',
            'content' => 'FastAPI si necesitas performance y async. DRF si quieres ORM robusto y admin panel out-of-the-box. Depende del proyecto.',
            'channel' => 'python',
            'parent_id' => $msg20->id,
            'created_at' => now()->subDays(5)->addHours(3),
        ]);

        Message::query()->create([
            'name' => 'Fernando',
            'content' => 'Llevo 2 años con FastAPI en producción. La documentación automática con Swagger es increíble. Y Pydantic para validación es chef\'s kiss 👌',
            'channel' => 'python',
            'parent_id' => $msg20->id,
            'created_at' => now()->subDays(5)->addHours(6),
        ]);

        Message::query()->create([
            'name' => 'Alberto',
            'content' => 'Proyecto de análisis de datos: pandas + numpy + matplotlib = combo perfecto. ¿Alguien usa Polars? He leído que es mucho más rápido que pandas.',
            'channel' => 'python',
            'created_at' => now()->subDays(4),
        ]);

        $msg24 = Message::query()->create([
            'name' => 'Isabel',
            'content' => 'Implementando modelo de ML con scikit-learn para clasificación. Accuracy del 94%! Próximo paso: probar con TensorFlow para deep learning. Tips?',
            'channel' => 'python',
            'created_at' => now()->subDays(3),
        ]);

        // Hilo sobre ML
        Message::query()->create([
            'name' => 'Carmen',
            'content' => 'Impresionante! 94% es excelente. Para TensorFlow te recomiendo empezar con Keras API, es más amigable. Y usa TensorBoard para visualizar el entrenamiento.',
            'channel' => 'python',
            'parent_id' => $msg24->id,
            'created_at' => now()->subDays(3)->addHours(2),
        ]);

        Message::query()->create([
            'name' => 'Pedro',
            'content' => 'Y no olvides data augmentation si tienes pocas muestras! Puede mejorar mucho la generalización del modelo.',
            'channel' => 'python',
            'parent_id' => $msg24->id,
            'created_at' => now()->subDays(3)->addHours(4),
        ]);

        Message::query()->create([
            'name' => 'Fernando',
            'content' => 'asyncio en Python es brutal para I/O bound tasks. Convertí mi scraper y pasó de 10 min a 30 seg. ¿Casos de uso donde async hizo gran diferencia?',
            'channel' => 'python',
            'created_at' => now()->subDays(2),
        ]);

        // Canal: devops - DevOps y infraestructura
        Message::query()->create([
            'name' => 'Patricia',
            'content' => 'Docker para dev: PHP-FPM + Nginx + PostgreSQL en un container, Python con uvicorn en otro. ¿Usan docker-compose o Kubernetes para desarrollo?',
            'channel' => 'devops',
            'created_at' => now()->subDays(4),
        ]);

        Message::query()->create([
            'name' => 'Andrés',
            'content' => 'Docker Compose para dev local, K8s para staging/prod. Minikube está bien para probar K8s localmente pero consume muchos recursos.',
            'channel' => 'devops',
            'created_at' => now()->subDays(3),
        ]);

        Message::query()->create([
            'name' => 'David',
            'content' => 'CI/CD con GitHub Actions es gratis para repos públicos y muy potente. Lo uso para tests automáticos, linting y deploy. ¿Alternativas que recomiendan?',
            'channel' => 'devops',
            'created_at' => now()->subDays(2),
        ]);

        // Canal: off-topic - Temas variados
        Message::query()->create([
            'name' => 'Beatriz',
            'content' => 'Proyecto fullstack: Laravel backend + Python microservicio para ML. Comunicación via API REST. ¿Mejores prácticas para integrar PHP y Python?',
            'channel' => 'off-topic',
            'created_at' => now()->subDays(3),
        ]);

        Message::query()->create([
            'name' => 'Andrés',
            'content' => 'Comparando ecosistemas: Composer vs pip, PHPStan vs mypy, Pest vs pytest. Ambos lenguajes tienen excelentes herramientas de calidad!',
            'channel' => 'off-topic',
            'created_at' => now()->subDays(1),
        ]);

        Message::query()->create([
            'name' => 'Sofia',
            'content' => '¿Alguien va a la PHPConf este año? Me encantaría conocer gente de la comunidad en persona! 🎉',
            'channel' => 'off-topic',
            'created_at' => now()->subHours(12),
        ]);

        $totalMessages = Message::count();
        $this->command->info(sprintf('✅ %s mensajes creados en 6 canales diferentes', $totalMessages));

        // Añadir reacciones variadas a mensajes principales y respuestas

        // Reacciones al mensaje de bienvenida
        Reaction::query()->create(['message_id' => $msg1->id, 'user_name' => 'María', 'emoji' => '🎉']);
        Reaction::query()->create(['message_id' => $msg1->id, 'user_name' => 'Jorge', 'emoji' => '🎉']);
        Reaction::query()->create(['message_id' => $msg1->id, 'user_name' => 'Ana', 'emoji' => '👍']);
        Reaction::query()->create(['message_id' => $msg1->id, 'user_name' => 'Laura', 'emoji' => '🚀']);
        Reaction::query()->create(['message_id' => $msg1->id, 'user_name' => 'Pedro', 'emoji' => '❤️']);

        // Reacciones al mensaje sobre Asia
        Reaction::query()->create(['message_id' => $msg2->id, 'user_name' => 'Carlos', 'emoji' => '🤔']);
        Reaction::query()->create(['message_id' => $msg2->id, 'user_name' => 'David', 'emoji' => '👍']);
        Reaction::query()->create(['message_id' => $msg2->id, 'user_name' => 'Jorge', 'emoji' => '🌏']);

        // Reacciones al mensaje de recursos
        Reaction::query()->create(['message_id' => $msg6->id, 'user_name' => 'María', 'emoji' => '💡']);
        Reaction::query()->create(['message_id' => $msg6->id, 'user_name' => 'David', 'emoji' => '�']);
        Reaction::query()->create(['message_id' => $msg6->id, 'user_name' => 'Patricia', 'emoji' => '�']);

        // Reacciones al mensaje de bienvenida PHP
        Reaction::query()->create(['message_id' => $msg11->id, 'user_name' => 'Carlos', 'emoji' => '❤️']);
        Reaction::query()->create(['message_id' => $msg11->id, 'user_name' => 'Sofia', 'emoji' => '🔥']);
        Reaction::query()->create(['message_id' => $msg11->id, 'user_name' => 'Miguel', 'emoji' => '�']);
        Reaction::query()->create(['message_id' => $msg11->id, 'user_name' => 'Roberto', 'emoji' => '�']);

        // Reacciones al mensaje sobre arquitectura Laravel
        Reaction::query()->create(['message_id' => $msg12->id, 'user_name' => 'Laura', 'emoji' => '🤔']);
        Reaction::query()->create(['message_id' => $msg12->id, 'user_name' => 'Elena', 'emoji' => '�']);
        Reaction::query()->create(['message_id' => $msg12->id, 'user_name' => 'Sofia', 'emoji' => '👍']);

        // Reacciones al mensaje de Pest vs PHPUnit
        Reaction::query()->create(['message_id' => $msg16->id, 'user_name' => 'Laura', 'emoji' => '�']);
        Reaction::query()->create(['message_id' => $msg16->id, 'user_name' => 'Sofia', 'emoji' => '👍']);
        Reaction::query()->create(['message_id' => $msg16->id, 'user_name' => 'Roberto', 'emoji' => '�']);

        // Reacciones al mensaje de bienvenida Python
        Reaction::query()->create(['message_id' => $msg19->id, 'user_name' => 'Carmen', 'emoji' => '🐍']);
        Reaction::query()->create(['message_id' => $msg19->id, 'user_name' => 'Alberto', 'emoji' => '🎉']);
        Reaction::query()->create(['message_id' => $msg19->id, 'user_name' => 'Isabel', 'emoji' => '�']);
        Reaction::query()->create(['message_id' => $msg19->id, 'user_name' => 'Fernando', 'emoji' => '❤️']);

        // Reacciones al mensaje de FastAPI vs Django
        Reaction::query()->create(['message_id' => $msg20->id, 'user_name' => 'Pedro', 'emoji' => '🤔']);
        Reaction::query()->create(['message_id' => $msg20->id, 'user_name' => 'Fernando', 'emoji' => '💡']);
        Reaction::query()->create(['message_id' => $msg20->id, 'user_name' => 'Isabel', 'emoji' => '👍']);

        // Reacciones al mensaje de ML
        Reaction::query()->create(['message_id' => $msg24->id, 'user_name' => 'Pedro', 'emoji' => '�']);
        Reaction::query()->create(['message_id' => $msg24->id, 'user_name' => 'Fernando', 'emoji' => '🚀']);
        Reaction::query()->create(['message_id' => $msg24->id, 'user_name' => 'Carmen', 'emoji' => '�']);
        Reaction::query()->create(['message_id' => $msg24->id, 'user_name' => 'Alberto', 'emoji' => '�']);

        $totalReactions = Reaction::count();
        $this->command->info(sprintf('✅ %s reacciones añadidas', $totalReactions));
        $this->command->info('');
        $this->command->info('🎉 ¡Base de datos poblada exitosamente!');
        $this->command->info('');
        $this->command->info('📊 Estadísticas finales:');
        $this->command->info('   - Mensajes totales: '.Message::count());
        $this->command->info('   - Mensajes principales: '.Message::whereNull('parent_id')->count());
        $this->command->info('   - Respuestas (hilos): '.Message::whereNotNull('parent_id')->count());
        $this->command->info('   - Reacciones: '.Reaction::count());
        $this->command->info('   - Usuarios únicos: '.Message::query()->distinct('name')->count('name'));
        $this->command->info('   - Canales activos: '.Message::query()->distinct('channel')->count('channel'));
        $this->command->info('');
        $this->command->info('📂 Canales disponibles:');

        $channels = Message::query()
            ->select('channel')
            ->selectRaw('COUNT(*) as message_count')
            ->whereNull('parent_id')
            ->groupBy('channel')
            ->orderByDesc('message_count')
            ->get();

        foreach ($channels as $channel) {
            $this->command->info(sprintf('   - #%s: %s mensajes', $channel->channel, $channel->message_count));
        }
    }
}
