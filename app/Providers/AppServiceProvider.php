<?php

namespace App\Providers;

use App\Models\Amistad;
use App\Models\Aporte;
use App\Models\Canal;
use App\Models\Comentarios;
use App\Models\Comunidad;
use App\Models\Etiqueta;
use App\Models\Foro;
use App\Models\Hilo;
use App\Models\Idioma;
use App\Models\Juego;
use App\Models\Mensaje;
use App\Models\noticias;
use App\Models\Notificacion;
use App\Models\Plataforma;
use App\Models\Problemas;
use App\Models\Publicacion;
use App\Models\ReporteModeracion;
use App\Models\ReporteSoporte;
use App\Policies\AmistadPolicy;
use App\Policies\AportePolicy;
use App\Policies\CanalPolicy;
use App\Policies\ComentariosPolicy;
use App\Policies\ComunidadPolicy;
use App\Policies\EtiquetaPolicy;
use App\Policies\ForoPolicy;
use App\Policies\HiloPolicy;
use App\Policies\IdiomaPolicy;
use App\Policies\JuegoPolicy;
use App\Policies\MensajePolicy;
use App\Policies\NoticiasPolicy;
use App\Policies\NotificacionPolicy;
use App\Policies\PlataformaPolicy;
use App\Policies\ProblemasPolicy;
use App\Policies\PublicacionPolicy;
use App\Policies\ReporteModeracionPolicy;
use App\Policies\ReporteSoportePolicy;
use Carbon\CarbonImmutable;
use Illuminate\Support\Facades\Date;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;
use Illuminate\Validation\Rules\Password;

class AppServiceProvider extends ServiceProvider
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
        $this->configureDefaults();

        Gate::policy(noticias::class, NoticiasPolicy::class);
        Gate::policy(Comentarios::class, ComentariosPolicy::class);
        Gate::policy(Comunidad::class, ComunidadPolicy::class);
        Gate::policy(Problemas::class, ProblemasPolicy::class);
        Gate::policy(Publicacion::class, PublicacionPolicy::class);
        Gate::policy(Foro::class, ForoPolicy::class);
        Gate::policy(Hilo::class, HiloPolicy::class);
        Gate::policy(Aporte::class, AportePolicy::class);
        Gate::policy(Canal::class, CanalPolicy::class);
        Gate::policy(Mensaje::class, MensajePolicy::class);
        Gate::policy(Amistad::class, AmistadPolicy::class);
        Gate::policy(Notificacion::class, NotificacionPolicy::class);
        Gate::policy(Juego::class, JuegoPolicy::class);
        Gate::policy(Plataforma::class, PlataformaPolicy::class);
        Gate::policy(Idioma::class, IdiomaPolicy::class);
        Gate::policy(Etiqueta::class, EtiquetaPolicy::class);
        Gate::policy(ReporteModeracion::class, ReporteModeracionPolicy::class);
        Gate::policy(ReporteSoporte::class, ReporteSoportePolicy::class);
    }

    /**
     * Configure default behaviors for production-ready applications.
     */
    protected function configureDefaults(): void
    {
        Date::use(CarbonImmutable::class);

        DB::prohibitDestructiveCommands(
            app()->isProduction(),
        );

        Password::defaults(fn (): ?Password => app()->isProduction()
            ? Password::min(12)
                ->mixedCase()
                ->letters()
                ->numbers()
                ->symbols()
                ->uncompromised()
            : null,
        );
    }
}
