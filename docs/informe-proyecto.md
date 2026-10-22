# Informe técnico y funcional de Nexus Community

## 1. Identificación del proyecto

**Nexus Community** es una plataforma web para jugadores y comunidades. Reúne noticias, búsqueda de comunidades, conversación entre usuarios, foros, canales, soporte y administración de equipos.

El repositorio contiene dos aplicaciones:

- **Aplicación principal Laravel**: gestiona usuarios, equipos, comunidades, contenido, autenticación, chat, reportes y paneles.
- **Community Directory**: servicio independiente en TypeScript, Express y Prisma que expone un CRUD REST para comunidades.

La aplicación Laravel usa PHP 8.3, Laravel 13, Blade, Livewire, Flux, Fortify, Eloquent, Vite y Tailwind CSS. La entrada se configura en `bootstrap/app.php`:

```php
return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->create();
```

## 2. Arquitectura

| Capa | Ubicación | Responsabilidad |
|---|---|---|
| Rutas | `routes/` | Define URI, método, nombre y middleware. |
| Controladores | `app/Http/Controllers/` | Valida solicitudes y coordina respuestas. |
| Modelos | `app/Models/` | Representa entidades y relaciones Eloquent. |
| Migraciones | `database/migrations/` | Define tablas, índices y claves foráneas. |
| Vistas | `resources/views/` | Renderiza páginas Blade. |
| Livewire | `app/Livewire/` | Implementa componentes interactivos. |
| Policies | `app/Policies/` | Autoriza acciones sobre recursos. |
| Seeders/factories | `database/seeders/`, `database/factories/` | Genera datos demo y de pruebas. |
| Frontend | `resources/css/`, `resources/js/` | Estilos y recursos compilados por Vite. |

El contexto de trabajo es el equipo actual. Las rutas `/{current_team}/...` requieren autenticación, correo verificado y pertenencia al equipo:

```php
Route::prefix('{current_team}')
    ->middleware([
        'auth',
        'verified',
        SetTeamUrlDefaults::class,
        EnsureTeamMembership::class,
    ])
    ->group(function (): void {
        Route::get('dashboard', [HomeController::class, 'index'])
            ->name('dashboard');
    });
```

## 3. Funcionalidades

### 3.1 Portal público

La ruta `/` muestra la portada y noticias oficiales sin equipo asociado. También existen páginas públicas para noticias, comunidades y soporte.

```php
$featuredNews = noticias::query()
    ->whereNull('team_id')
    ->where('es_oficial', true)
    ->with(['comentarios' => fn ($query) => $query->with('autor')->latest()])
    ->latest()
    ->limit(5)
    ->get();

return view('nexus_community', [
    'officialNews' => $featuredNews,
    'usuarios_activos' => $usuariosActivos,
    'post_diarios' => $postsDiarios,
    'telemetria' => $telemetria,
]);
```

Incluye consulta paginada de noticias, actualización JSON, catálogo filtrable de comunidades, solicitud de ingreso y soporte público. Un comentario de un visitante se conserva temporalmente en sesión y se procesa después del login o registro.

### 3.2 Autenticación y configuración

Fortify gestiona registro, login, logout, verificación de correo, recuperación de contraseña, confirmación de contraseña, 2FA, códigos de recuperación y passkeys/WebAuthn.

```php
Auth::guard('web')->logout();
Session::invalidate();
Session::regenerateToken();
return redirect('/');
```

Las opciones de `/settings` son perfil, apariencia, seguridad, equipos, edición de equipos y creación de comunidades. Las páginas interactivas de configuración usan Livewire:

```php
Route::livewire('settings/profile', 'pages::settings.profile')
    ->name('profile.edit');
Route::livewire('settings/security', 'pages::settings.security')
    ->name('security.edit');
```

### 3.3 Equipos y dashboard

Los usuarios pueden pertenecer a varios equipos. `teams`, `team_members` y `team_invitations` almacenan equipos, roles e invitaciones. El dashboard muestra noticias recientes, comunidades, comentarios, problemas abiertos, notificaciones y usuarios para chat.

```php
'problemas_abiertos' => Problemas::query()
    ->whereIn('estado', ['abierto', 'en_progreso'])
    ->count(),
```

La autorización se aplica con middleware, `Gate::authorize()` y policies. Ocultar un botón en Blade no sustituye la protección del controlador.

### 3.4 Noticias y comentarios

El módulo permite CRUD de noticias, búsqueda, filtros, paginación, imágenes por URL o archivo y comentarios. También responde JSON cuando el cliente lo solicita.

```php
$validated = $request->validate([
    'titulo' => ['required', 'string', 'max:180'],
    'contenido' => ['required', 'string', 'max:10000'],
    'imagen' => ['nullable', 'image', 'max:4096'],
]);

$noticia = noticias::create([
    ...$validated,
    'team_id' => $team->id,
    'user_id' => $request->user()->id,
]);
```

### 3.5 Comunidades

Las comunidades se pueden buscar por juego, plataforma, región, idioma, modalidad, horario, tipo, nivel, rango y estado. Incluyen imagen, miembros, solicitudes, roles, invitaciones y transferencia de propiedad.

La creación usa una transacción para registrar al creador como administrador:

```php
$comunidad = DB::transaction(function () use ($request, $team, $validated) {
    $comunidad = Comunidad::create([
        ...$validated,
        'team_id' => $team->id,
        'creador_id' => $request->user()->id,
    ]);
    $comunidad->miembros()->syncWithoutDetaching([
        $request->user()->id => ['rol' => 'admin'],
    ]);
    return $comunidad;
});
```

### 3.6 Interacción social

El usuario puede crear publicaciones, comentarios y reacciones; participar en foros, hilos y aportes; usar canales de texto o voz; enviar mensajes privados; gestionar amistades y consultar notificaciones.

El chat privado evita enviarse mensajes a sí mismo y marca como leídos los mensajes recibidos:

```php
if ($request->user()->is($recipient)) {
    return back()->withErrors([
        'contenido' => __('You cannot send a message to yourself.'),
    ]);
}
```

También existe un chatbot protegido por autenticación, verificación y `throttle:30,1`.

### 3.7 Catálogos y soporte

Juegos, plataformas, idiomas y etiquetas son catálogos CRUD. Los juegos se relacionan con plataformas e idiomas mediante tablas intermedias.

El módulo de incidencias diferencia problemas del equipo, reportes de soporte y reportes de moderación. Los reportes de soporte tienen etiquetas, respuestas y votos. Moderación maneja los estados `pendiente`, `revisado` y `rechazado`.

```php
Gate::authorize('update', [$reporteModeracion, $team]);
$reporteModeracion->update(
    $request->validate([
        'estado' => 'required|in:pendiente,revisado,rechazado',
    ])
);
```

### 3.8 Noticias oficiales automáticas

`news:sync-official` consulta feeds RSS/XML de PlayStation Blog, Xbox Wire y Nintendo News, limita importaciones y evita duplicados mediante URL o hash SHA-256.

```bash
php artisan news:sync-official --limit=10
```

La tarea está programada cada seis horas y no permite ejecuciones solapadas:

```php
Schedule::command('news:sync-official --limit=10')
    ->everySixHours()
    ->withoutOverlapping();
```

## 4. Rutas Laravel

### 4.1 Públicas

| Método | URI | Nombre | Función |
|---|---|---|---|
| GET | `/` | `home` | Portada y noticias oficiales. |
| GET | `/noticias-publicas` | `public-noticias.index` | Noticias paginadas. |
| GET | `/noticias-oficiales/refresh` | `official-news.refresh` | Actualización JSON. |
| GET | `/comunidad-publica` | `public-comunidad.index` | Catálogo público. |
| POST | `/comunidad-publica/{comunidad}/solicitar` | `public-comunidad.apply` | Solicitud de ingreso. |
| GET | `/soporte-publico` | `public-problemas.index` | Ayuda pública. |
| POST | `/noticias-oficiales/{noticia}/comentarios` | `official-news.comments.store` | Crear comentario. |

### 4.2 Autenticadas generales

Requieren `auth` y `verified`:

```php
Route::get('chat/{recipient}', [ChatController::class, 'show'])
    ->name('chat.show');
Route::post('chat/{recipient}', [ChatController::class, 'store'])
    ->name('chat.store');
Route::get('amistades', [AmistadController::class, 'index'])
    ->name('amistades.index');
Route::get('notificaciones', [NotificacionController::class, 'index'])
    ->name('notificaciones.index');
```

### 4.3 Configuración

```text
GET  /settings/profile
GET  /settings/appearance
GET  /settings/security
GET  /settings/teams
GET  /settings/teams/{team}
GET  /settings/communities
GET  /.well-known/passkey-endpoints
```

### 4.4 Rutas por equipo

Todas usan el prefijo `/{current_team}` y middleware de autenticación, verificación y pertenencia:

```php
Route::resource('noticias', NoticiasController::class);
Route::resource('comentarios', ComentariosController::class);
Route::resource('comunidad', ComunidadController::class);
Route::resource('problemas', ProblemasController::class);
Route::resource('publicaciones', PublicacionController::class);
Route::resource('foros', ForoController::class);
Route::resource('hilos', HiloController::class);
Route::resource('aportes', AporteController::class);
Route::resource('canales', CanalController::class);
Route::resource('juegos', JuegoController::class);
Route::resource('plataformas', PlataformaController::class);
Route::resource('idiomas', IdiomaController::class);
Route::resource('etiquetas', EtiquetaController::class);
```

Cada `Route::resource` genera `index`, `create`, `store`, `show`, `edit`, `update` y `destroy`. Las rutas adicionales más importantes son:

```text
/{current_team}/dashboard
/{current_team}/canales/{canal}/mensajes
/{current_team}/publicaciones/{publicacion}/reacciones
/{current_team}/reportes-moderacion
/{current_team}/reportes-moderacion/{reporteModeracion}/estado
/{current_team}/reportes-soporte/{reporteSoporte}/responder
/{current_team}/reportes-soporte/{reporteSoporte}/votar
```

Para consultar rutas reales:

```bash
php artisan route:list
php artisan route:list --path=dashboard
php artisan route:list --name=comunidad
php artisan route:list --except-vendor
```

## 5. Community Directory: API Express

El subproyecto usa Express, TypeScript y Prisma. Su base URL es `/api/communities`:

| Método | Endpoint | Operación |
|---|---|---|
| POST | `/api/communities` | Crear comunidad. |
| GET | `/api/communities` | Listar comunidades. |
| GET | `/api/communities/:id` | Consultar comunidad. |
| PUT | `/api/communities/:id` | Actualizar comunidad. |
| DELETE | `/api/communities/:id` | Eliminar comunidad. |

```ts
app.use('/api/communities', router);
router.post('/', communityController.createCommunity.bind(communityController));
router.get('/', communityController.getAllCommunities.bind(communityController));
router.get('/:id', communityController.getCommunityById.bind(communityController));
router.put('/:id', communityController.updateCommunity.bind(communityController));
router.delete('/:id', communityController.deleteCommunity.bind(communityController));
```

```bash
cd community-directory
npm install
npm run dev
npm run build
npm run seed
npx prisma migrate dev
npx prisma db seed
```

## 6. Comandos Artisan y desarrollo

### Instalación y ejecución

```bash
composer install
copy .env.example .env
php artisan key:generate
php artisan migrate
npm install
npm run build
php artisan storage:link
php artisan serve
npm run dev
```

En PowerShell:

```powershell
Copy-Item .env.example .env
```

Comando agrupado:

```bash
composer run dev
```

### Base de datos y componentes

```bash
php artisan migrate
php artisan migrate:status
php artisan migrate:rollback
php artisan db:seed
php artisan migrate:fresh --seed
php artisan tinker
php artisan make:model Juego -mcrf
php artisan make:migration add_estado_to_juegos_table --table=juegos
php artisan make:controller JuegoController --resource --model=Juego
php artisan make:request StoreJuegoRequest
php artisan make:policy JuegoPolicy --model=Juego
php artisan make:middleware CheckUserRole
php artisan make:livewire JuegoTable
```

`migrate:fresh --seed` elimina las tablas y solo debe usarse en desarrollo. El seeder crea cuenta de prueba, equipo demo, usuarios, noticias, comunidades, conversaciones, foros, canales, problemas y notificaciones.

### Calidad y diagnóstico

```bash
php artisan test
php artisan test --filter=TeamTest
composer lint
composer lint:check
composer types:check
composer run ci:check
php artisan optimize:clear
php artisan view:clear
php artisan config:clear
php artisan route:clear
```

Scripts definidos en `composer.json`:

| Script | Uso |
|---|---|
| `composer setup` | Instala, configura, migra y compila. |
| `composer run dev` | Inicia desarrollo. |
| `composer lint` | Formatea con Pint. |
| `composer lint:check` | Verifica formato. |
| `composer types:check` | Ejecuta PHPStan/Larastan. |
| `composer test` | Ejecuta calidad y pruebas. |
| `composer run ci:check` | Ejecuta la comprobación de CI. |

## 7. Uso de Git y GitHub

### Configuración y descarga

```bash
git config --global user.name "Nombre Apellido"
git config --global user.email "correo@ejemplo.com"
git config --list
git clone <URL_DEL_REPOSITORIO>
cd comunity-gamer-version-estable
git status
git branch --show-current
git log --oneline --decorate -10
git remote -v
```

### Rama de trabajo

```bash
git fetch origin
git switch main
git pull --ff-only origin main
git switch -c feature/mejora-comunidades
```

### Revisar, guardar y publicar

```bash
git status
git diff
git diff --check
git add app/Http/Controllers/ComunidadController.php routes/web.php
git diff --cached
git commit -m "feat: mejorar filtros de comunidades"
git push -u origin feature/mejora-comunidades
```

Mensajes recomendados:

```text
feat: agregar filtro de comunidades
fix: corregir autorización de reportes
test: cubrir creación de equipos
docs: actualizar informe técnico
refactor: separar consulta del dashboard
```

### Actualizar y resolver conflictos

```bash
git fetch origin
git rebase origin/main
git status
git add <archivo-resuelto>
git rebase --continue
git push --force-with-lease
```

Alternativa usando merge:

```bash
git switch feature/mejora-comunidades
git merge origin/main
git push
```

### Revisión y recuperación

```bash
git log --oneline --graph --all
git show <commit>
git diff main...feature/mejora-comunidades
git restore --staged <archivo>
git restore <archivo>
git stash push -m "trabajo temporal"
git stash list
git stash pop
```

`git restore <archivo>` descarta cambios locales de ese archivo; debe usarse solo cuando esos cambios no sean necesarios.

## 8. Flujo recomendado

```text
1. Crear una rama Git.
2. Revisar ruta, controlador, policy, modelo y vista.
3. Implementar validación y autorización.
4. Crear o ajustar una prueba Feature/Unit.
5. Ejecutar php artisan test --filter=NombreDeLaPrueba.
6. Ejecutar composer lint:check y composer types:check.
7. Revisar git diff y git diff --check.
8. Crear un commit pequeño y descriptivo.
9. Publicar la rama y abrir el Pull Request.
```

## 9. Conclusiones y recomendaciones

Nexus Community funciona como una plataforma social multi-equipo con separación entre contenido público e interno. El acceso se controla en dos niveles: middleware para contexto y pertenencia, y policies para cada operación.

- Mantener `APP_DEBUG=false` en producción.
- No ejecutar `migrate:fresh --seed` contra una base real.
- Cambiar o retirar la cuenta demo antes de publicar.
- Ejecutar `npm run build` antes del despliegue.
- Probar cambios en rutas, policies, migraciones y seeders.
- Mantener `public/storage` enlazado para imágenes subidas.
- Verificar la disponibilidad de feeds externos antes de depender de la sincronización.
- No confiar en ocultar botones: la seguridad debe permanecer en middleware, policies y controladores.

## 10. Archivos de referencia

- [Documentación funcional](funcionalidades.md)
- [Rutas web](../routes/web.php)
- [Rutas de configuración](../routes/settings.php)
- [Programación de tareas](../routes/console.php)
- [Comando de noticias](../app/Console/Commands/SyncOfficialNews.php)
- [Configuración Composer](../composer.json)
- [Configuración frontend](../package.json)
- [API Community Directory](../community-directory/src/routes/communityRoutes.ts)
- [Pruebas de equipos](../tests/Feature/Teams/TeamTest.php)