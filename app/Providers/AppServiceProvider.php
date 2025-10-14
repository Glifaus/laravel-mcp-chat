<?php

declare(strict_types=1);

namespace App\Providers;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\ServiceProvider;

final class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Desactivamos el guard
        Model::unguard();
        // Si usamos SQLite en /tmp (Vercel), aseguramos que la DB existe y está migrada
        if (config('database.default') === 'sqlite') {
            $path = config('database.connections.sqlite.database');

            // En Vercel solo /tmp es escribible
            if (is_string($path) && str_starts_with($path, '/tmp')) {
                if (! file_exists($path)) {
                    @touch($path); // crea SQLite vacío
                }

                // Intenta migrar (idempotente con --force)
                try {
                    Artisan::call('migrate', ['--force' => true]);
                } catch (\Throwable $e) {
                    // Silencia o loguea a stderr si lo prefieres
                    // \Log::error('Migrate failed: '.$e->getMessage());
                }
            }
        }
    }
}
