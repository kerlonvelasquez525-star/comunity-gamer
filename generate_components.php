<?php

$baseDir = 'C:\laravel\comunity-gamer-version-estable';

$files = [
    // MODELS ---------------------------------------------------------
    'app/Models/Juego.php' => <<<PHP
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

/**
 * Modelo Juego
 * @property int \$id
 * @property string \$titulo
 * @property string|null \$descripcion
 * @property string|null \$desarrollador
 * @property \Illuminate\Support\Carbon|null \$fecha_lanzamiento
 */
class Juego extends Model
{
    protected \$table = 'juegos';
    
    protected \$fillable = [
        'titulo',
        'descripcion',
        'desarrollador',
        'fecha_lanzamiento'
    ];

    protected \$casts = [
        'fecha_lanzamiento' => 'date'
    ];

    /**
     * Plataformas del juego
     */
    public function plataformas(): BelongsToMany
    {
        return \$this->belongsToMany(Plataforma::class, 'juego_plataforma');
    }

    /**
     * Idiomas del juego
     */
    public function idiomas(): BelongsToMany
    {
        return \$this->belongsToMany(Idioma::class, 'juego_idioma');
    }
}
PHP,

    'app/Models/Plataforma.php' => <<<PHP
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

/**
 * Modelo Plataforma
 * @property int \$id
 * @property string \$nombre
 */
class Plataforma extends Model
{
    protected \$table = 'plataformas';
    
    protected \$fillable = ['nombre'];

    /**
     * Juegos en esta plataforma
     */
    public function juegos(): BelongsToMany
    {
        return \$this->belongsToMany(Juego::class, 'juego_plataforma');
    }
}
PHP,

    'app/Models/Idioma.php' => <<<PHP
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

/**
 * Modelo Idioma
 * @property int \$id
 * @property string \$codigo
 * @property string \$nombre
 */
class Idioma extends Model
{
    protected \$table = 'idiomas';
    
    protected \$fillable = [
        'codigo',
        'nombre'
    ];

    /**
     * Juegos en este idioma
     */
    public function juegos(): BelongsToMany
    {
        return \$this->belongsToMany(Juego::class, 'juego_idioma');
    }
}
PHP,

    'app/Models/Etiqueta.php' => <<<PHP
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

/**
 * Modelo Etiqueta
 * @property int \$id_etiqueta
 * @property string \$nombre
 */
class Etiqueta extends Model
{
    protected \$table = 'etiquetas';
    protected \$primaryKey = 'id_etiqueta';
    
    protected \$fillable = ['nombre'];

    /**
     * Reportes de soporte con esta etiqueta
     */
    public function reportesSoporte(): BelongsToMany
    {
        return \$this->belongsToMany(ReporteSoporte::class, 'reportes_etiquetas', 'id_etiqueta', 'id_reporte');
    }
}
PHP,

    'app/Models/ReporteModeracion.php' => <<<PHP
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Modelo ReporteModeracion
 * @property int \$id_reporte
 * @property int \$id_reportador
 * @property int|null \$id_reportado
 * @property string \$motivo
 * @property string \$estado
 * @property string \$fecha
 */
class ReporteModeracion extends Model
{
    protected \$table = 'reportes_moderacion';
    protected \$primaryKey = 'id_reporte';
    public \$timestamps = false;
    
    protected \$fillable = [
        'id_reportador',
        'id_reportado',
        'motivo',
        'estado'
    ];

    /**
     * Usuario que reporta
     */
    public function reportador(): BelongsTo
    {
        return \$this->belongsTo(User::class, 'id_reportador');
    }

    /**
     * Usuario reportado
     */
    public function reportado(): BelongsTo
    {
        return \$this->belongsTo(User::class, 'id_reportado');
    }
}
PHP,

    'app/Models/ReporteSoporte.php' => <<<PHP
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

/**
 * Modelo ReporteSoporte
 * @property int \$id_reporte
 * @property int \$id_usuario
 * @property string \$asunto
 * @property string \$descripcion
 * @property string \$estado
 * @property string \$fecha_creacion
 */
class ReporteSoporte extends Model
{
    protected \$table = 'reportes_soporte';
    protected \$primaryKey = 'id_reporte';
    public \$timestamps = false;
    
    protected \$fillable = [
        'id_usuario',
        'asunto',
        'descripcion',
        'estado'
    ];

    /**
     * Usuario que crea el reporte
     */
    public function usuario(): BelongsTo
    {
        return \$this->belongsTo(User::class, 'id_usuario');
    }

    /**
     * Respuestas del reporte
     */
    public function respuestas(): HasMany
    {
        return \$this->hasMany(RespuestaSoporte::class, 'id_reporte');
    }

    /**
     * Votos del reporte
     */
    public function votos(): HasMany
    {
        return \$this->hasMany(VotoSoporte::class, 'id_reporte');
    }

    /**
     * Etiquetas del reporte
     */
    public function etiquetas(): BelongsToMany
    {
        return \$this->belongsToMany(Etiqueta::class, 'reportes_etiquetas', 'id_reporte', 'id_etiqueta');
    }
}
PHP,

    'app/Models/RespuestaSoporte.php' => <<<PHP
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Modelo RespuestaSoporte
 * @property int \$id_respuesta
 * @property int \$id_reporte
 * @property int \$id_usuario
 * @property string \$mensaje
 * @property string \$fecha_respuesta
 */
class RespuestaSoporte extends Model
{
    protected \$table = 'respuestas_soporte';
    protected \$primaryKey = 'id_respuesta';
    public \$timestamps = false;
    
    protected \$fillable = [
        'id_reporte',
        'id_usuario',
        'mensaje'
    ];

    /**
     * Reporte al que pertenece
     */
    public function reporte(): BelongsTo
    {
        return \$this->belongsTo(ReporteSoporte::class, 'id_reporte', 'id_reporte');
    }

    /**
     * Usuario que responde
     */
    public function usuario(): BelongsTo
    {
        return \$this->belongsTo(User::class, 'id_usuario');
    }
}
PHP,

    'app/Models/VotoSoporte.php' => <<<PHP
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Modelo VotoSoporte
 * @property int \$id_voto
 * @property int \$id_reporte
 * @property int \$id_usuario
 * @property string \$voto
 */
class VotoSoporte extends Model
{
    protected \$table = 'votos_soporte';
    protected \$primaryKey = 'id_voto';
    public \$timestamps = false;
    
    protected \$fillable = [
        'id_reporte',
        'id_usuario',
        'voto'
    ];

    /**
     * Reporte asociado
     */
    public function reporte(): BelongsTo
    {
        return \$this->belongsTo(ReporteSoporte::class, 'id_reporte', 'id_reporte');
    }

    /**
     * Usuario que vota
     */
    public function usuario(): BelongsTo
    {
        return \$this->belongsTo(User::class, 'id_usuario');
    }
}
PHP,

    // POLICIES ---------------------------------------------------------
    'app/Policies/JuegoPolicy.php' => <<<PHP
<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Juego;
use App\Models\Team;
use App\Enums\TeamRole;
use Illuminate\Auth\Access\HandlesAuthorization;

class JuegoPolicy
{
    use HandlesAuthorization;

    public function viewAny(User \$user, Team \$team): bool { return \$user->belongsToTeam(\$team); }
    public function view(User \$user, Juego \$model, Team \$team): bool { return \$user->belongsToTeam(\$team); }
    public function create(User \$user, Team \$team): bool { return \$this->isAdmin(\$user, \$team); }
    public function update(User \$user, Juego \$model, Team \$team): bool { return \$this->isAdmin(\$user, \$team); }
    public function delete(User \$user, Juego \$model, Team \$team): bool { return \$this->isAdmin(\$user, \$team); }
    
    private function isAdmin(User \$user, Team \$team): bool 
    { 
        return \$user->belongsToTeam(\$team) && \$user->teamRole(\$team)?->isAtLeast(TeamRole::Admin) === true; 
    }
}
PHP,

    'app/Policies/PlataformaPolicy.php' => <<<PHP
<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Plataforma;
use App\Models\Team;
use App\Enums\TeamRole;
use Illuminate\Auth\Access\HandlesAuthorization;

class PlataformaPolicy
{
    use HandlesAuthorization;

    public function viewAny(User \$user, Team \$team): bool { return \$user->belongsToTeam(\$team); }
    public function view(User \$user, Plataforma \$model, Team \$team): bool { return \$user->belongsToTeam(\$team); }
    public function create(User \$user, Team \$team): bool { return \$this->isAdmin(\$user, \$team); }
    public function update(User \$user, Plataforma \$model, Team \$team): bool { return \$this->isAdmin(\$user, \$team); }
    public function delete(User \$user, Plataforma \$model, Team \$team): bool { return \$this->isAdmin(\$user, \$team); }
    
    private function isAdmin(User \$user, Team \$team): bool 
    { 
        return \$user->belongsToTeam(\$team) && \$user->teamRole(\$team)?->isAtLeast(TeamRole::Admin) === true; 
    }
}
PHP,

    'app/Policies/IdiomaPolicy.php' => <<<PHP
<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Idioma;
use App\Models\Team;
use App\Enums\TeamRole;
use Illuminate\Auth\Access\HandlesAuthorization;

class IdiomaPolicy
{
    use HandlesAuthorization;

    public function viewAny(User \$user, Team \$team): bool { return \$user->belongsToTeam(\$team); }
    public function view(User \$user, Idioma \$model, Team \$team): bool { return \$user->belongsToTeam(\$team); }
    public function create(User \$user, Team \$team): bool { return \$this->isAdmin(\$user, \$team); }
    public function update(User \$user, Idioma \$model, Team \$team): bool { return \$this->isAdmin(\$user, \$team); }
    public function delete(User \$user, Idioma \$model, Team \$team): bool { return \$this->isAdmin(\$user, \$team); }
    
    private function isAdmin(User \$user, Team \$team): bool 
    { 
        return \$user->belongsToTeam(\$team) && \$user->teamRole(\$team)?->isAtLeast(TeamRole::Admin) === true; 
    }
}
PHP,

    'app/Policies/EtiquetaPolicy.php' => <<<PHP
<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Etiqueta;
use App\Models\Team;
use App\Enums\TeamRole;
use Illuminate\Auth\Access\HandlesAuthorization;

class EtiquetaPolicy
{
    use HandlesAuthorization;

    public function viewAny(User \$user, Team \$team): bool { return \$user->belongsToTeam(\$team); }
    public function view(User \$user, Etiqueta \$model, Team \$team): bool { return \$user->belongsToTeam(\$team); }
    public function create(User \$user, Team \$team): bool { return \$this->isAdmin(\$user, \$team); }
    public function update(User \$user, Etiqueta \$model, Team \$team): bool { return \$this->isAdmin(\$user, \$team); }
    public function delete(User \$user, Etiqueta \$model, Team \$team): bool { return \$this->isAdmin(\$user, \$team); }
    
    private function isAdmin(User \$user, Team \$team): bool 
    { 
        return \$user->belongsToTeam(\$team) && \$user->teamRole(\$team)?->isAtLeast(TeamRole::Admin) === true; 
    }
}
PHP,

    'app/Policies/ReporteModeracionPolicy.php' => <<<PHP
<?php

namespace App\Policies;

use App\Models\User;
use App\Models\ReporteModeracion;
use App\Models\Team;
use App\Enums\TeamRole;
use Illuminate\Auth\Access\HandlesAuthorization;

class ReporteModeracionPolicy
{
    use HandlesAuthorization;

    public function viewAny(User \$user, Team \$team): bool { return \$this->isAdmin(\$user, \$team); }
    public function view(User \$user, ReporteModeracion \$model, Team \$team): bool { return \$this->isAdmin(\$user, \$team); }
    public function create(User \$user, Team \$team): bool { return \$user->belongsToTeam(\$team); }
    public function updateEstado(User \$user, ReporteModeracion \$model, Team \$team): bool { return \$this->isAdmin(\$user, \$team); }
    
    private function isAdmin(User \$user, Team \$team): bool 
    { 
        return \$user->belongsToTeam(\$team) && \$user->teamRole(\$team)?->isAtLeast(TeamRole::Admin) === true; 
    }
}
PHP,

    'app/Policies/ReporteSoportePolicy.php' => <<<PHP
<?php

namespace App\Policies;

use App\Models\User;
use App\Models\ReporteSoporte;
use App\Models\Team;
use App\Enums\TeamRole;
use Illuminate\Auth\Access\HandlesAuthorization;

class ReporteSoportePolicy
{
    use HandlesAuthorization;

    public function viewAny(User \$user, Team \$team): bool { return \$user->belongsToTeam(\$team); }
    public function view(User \$user, ReporteSoporte \$model, Team \$team): bool 
    { 
        return \$user->id === \$model->id_usuario || \$this->isAdmin(\$user, \$team); 
    }
    public function create(User \$user, Team \$team): bool { return \$user->belongsToTeam(\$team); }
    public function update(User \$user, ReporteSoporte \$model, Team \$team): bool { return \$this->isAdmin(\$user, \$team); }
    public function delete(User \$user, ReporteSoporte \$model, Team \$team): bool { return \$this->isAdmin(\$user, \$team); }
    
    private function isAdmin(User \$user, Team \$team): bool 
    { 
        return \$user->belongsToTeam(\$team) && \$user->teamRole(\$team)?->isAtLeast(TeamRole::Admin) === true; 
    }
}
PHP,

    // CONTROLLERS ---------------------------------------------------------
    'app/Http/Controllers/JuegoController.php' => <<<PHP
<?php

namespace App\Http\Controllers;

use App\Models\Juego;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Gate;

class JuegoController extends Controller
{
    public function index(Request \$request, string \$current_team): Response
    {
        \$team = \$this->currentTeam(\$request);
        Gate::authorize('viewAny', [Juego::class, \$team]);

        \$query = Juego::withCount(['plataformas', 'idiomas']);
        
        if (\$search = \$request->input('search')) {
            \$query->where('titulo', 'like', "%{\$search}%");
        }
        
        \$items = \$query->paginate(15);

        if (\$request->expectsJson()) {
            return response()->json(\$items);
        }

        return response()->view('gamer.index', [
            'items' => \$items,
            'title' => 'Juegos',
            'resource' => 'juegos',
            'team' => \$team
        ]);
    }

    public function store(Request \$request, string \$current_team): Response
    {
        \$team = \$this->currentTeam(\$request);
        Gate::authorize('create', [Juego::class, \$team]);

        \$validated = \$request->validate([
            'titulo' => 'required|string|max:255',
            'descripcion' => 'nullable|string|max:5000',
            'desarrollador' => 'nullable|string|max:255',
            'fecha_lanzamiento' => 'nullable|date',
            'plataformas' => 'nullable|array',
            'plataformas.*' => 'integer|exists:plataformas,id',
            'idiomas' => 'nullable|array',
            'idiomas.*' => 'integer|exists:idiomas,id',
        ]);

        \$juego = Juego::create(\$validated);
        
        if (isset(\$validated['plataformas'])) {
            \$juego->plataformas()->sync(\$validated['plataformas']);
        }
        if (isset(\$validated['idiomas'])) {
            \$juego->idiomas()->sync(\$validated['idiomas']);
        }

        if (\$request->expectsJson()) {
            return response()->json(\$juego, 201);
        }

        return redirect()->route('juegos.index', ['current_team' => \$current_team]);
    }

    public function show(Request \$request, string \$current_team, Juego \$juego): Response
    {
        \$team = \$this->currentTeam(\$request);
        Gate::authorize('view', [\$juego, \$team]);

        \$juego->load(['plataformas', 'idiomas']);

        if (\$request->expectsJson()) {
            return response()->json(\$juego);
        }

        return response()->view('gamer.show', [
            'item' => \$juego,
            'title' => 'Juego: ' . \$juego->titulo,
            'resource' => 'juegos',
            'team' => \$team
        ]);
    }

    public function update(Request \$request, string \$current_team, Juego \$juego): Response
    {
        \$team = \$this->currentTeam(\$request);
        Gate::authorize('update', [\$juego, \$team]);

        \$validated = \$request->validate([
            'titulo' => 'sometimes|required|string|max:255',
            'descripcion' => 'nullable|string|max:5000',
            'desarrollador' => 'nullable|string|max:255',
            'fecha_lanzamiento' => 'nullable|date',
            'plataformas' => 'nullable|array',
            'plataformas.*' => 'integer|exists:plataformas,id',
            'idiomas' => 'nullable|array',
            'idiomas.*' => 'integer|exists:idiomas,id',
        ]);

        \$juego->update(\$validated);

        if (isset(\$validated['plataformas'])) {
            \$juego->plataformas()->sync(\$validated['plataformas']);
        }
        if (isset(\$validated['idiomas'])) {
            \$juego->idiomas()->sync(\$validated['idiomas']);
        }

        if (\$request->expectsJson()) {
            return response()->json(\$juego);
        }

        return redirect()->route('juegos.show', ['current_team' => \$current_team, 'juego' => \$juego]);
    }

    public function destroy(Request \$request, string \$current_team, Juego \$juego): Response
    {
        \$team = \$this->currentTeam(\$request);
        Gate::authorize('delete', [\$juego, \$team]);

        \$juego->delete();

        if (\$request->expectsJson()) {
            return response()->json(null, 204);
        }

        return redirect()->route('juegos.index', ['current_team' => \$current_team]);
    }
}
PHP,

    'app/Http/Controllers/PlataformaController.php' => <<<PHP
<?php

namespace App\Http\Controllers;

use App\Models\Plataforma;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Gate;

class PlataformaController extends Controller
{
    public function index(Request \$request, string \$current_team): Response
    {
        \$team = \$this->currentTeam(\$request);
        Gate::authorize('viewAny', [Plataforma::class, \$team]);

        \$query = Plataforma::query();
        
        if (\$search = \$request->input('search')) {
            \$query->where('nombre', 'like', "%{\$search}%");
        }
        
        \$items = \$query->paginate(15);

        if (\$request->expectsJson()) {
            return response()->json(\$items);
        }

        return response()->view('gamer.index', [
            'items' => \$items,
            'title' => 'Plataformas',
            'resource' => 'plataformas',
            'team' => \$team
        ]);
    }

    public function store(Request \$request, string \$current_team): Response
    {
        \$team = \$this->currentTeam(\$request);
        Gate::authorize('create', [Plataforma::class, \$team]);

        \$validated = \$request->validate([
            'nombre' => 'required|string|max:255|unique:plataformas'
        ]);

        \$plataforma = Plataforma::create(\$validated);

        if (\$request->expectsJson()) {
            return response()->json(\$plataforma, 201);
        }

        return redirect()->route('plataformas.index', ['current_team' => \$current_team]);
    }

    public function show(Request \$request, string \$current_team, Plataforma \$plataforma): Response
    {
        \$team = \$this->currentTeam(\$request);
        Gate::authorize('view', [\$plataforma, \$team]);

        if (\$request->expectsJson()) {
            return response()->json(\$plataforma);
        }

        return response()->view('gamer.show', [
            'item' => \$plataforma,
            'title' => 'Plataforma: ' . \$plataforma->nombre,
            'resource' => 'plataformas',
            'team' => \$team
        ]);
    }

    public function update(Request \$request, string \$current_team, Plataforma \$plataforma): Response
    {
        \$team = \$this->currentTeam(\$request);
        Gate::authorize('update', [\$plataforma, \$team]);

        \$validated = \$request->validate([
            'nombre' => 'sometimes|required|string|max:255|unique:plataformas,nombre,' . \$plataforma->id
        ]);

        \$plataforma->update(\$validated);

        if (\$request->expectsJson()) {
            return response()->json(\$plataforma);
        }

        return redirect()->route('plataformas.show', ['current_team' => \$current_team, 'plataforma' => \$plataforma]);
    }

    public function destroy(Request \$request, string \$current_team, Plataforma \$plataforma): Response
    {
        \$team = \$this->currentTeam(\$request);
        Gate::authorize('delete', [\$plataforma, \$team]);

        \$plataforma->delete();

        if (\$request->expectsJson()) {
            return response()->json(null, 204);
        }

        return redirect()->route('plataformas.index', ['current_team' => \$current_team]);
    }
}
PHP,

    'app/Http/Controllers/IdiomaController.php' => <<<PHP
<?php

namespace App\Http\Controllers;

use App\Models\Idioma;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Gate;

class IdiomaController extends Controller
{
    public function index(Request \$request, string \$current_team): Response
    {
        \$team = \$this->currentTeam(\$request);
        Gate::authorize('viewAny', [Idioma::class, \$team]);

        \$query = Idioma::query();
        
        if (\$search = \$request->input('search')) {
            \$query->where('nombre', 'like', "%{\$search}%")
                  ->orWhere('codigo', 'like', "%{\$search}%");
        }
        
        \$items = \$query->paginate(15);

        if (\$request->expectsJson()) {
            return response()->json(\$items);
        }

        return response()->view('gamer.index', [
            'items' => \$items,
            'title' => 'Idiomas',
            'resource' => 'idiomas',
            'team' => \$team
        ]);
    }

    public function store(Request \$request, string \$current_team): Response
    {
        \$team = \$this->currentTeam(\$request);
        Gate::authorize('create', [Idioma::class, \$team]);

        \$validated = \$request->validate([
            'codigo' => 'required|string|max:5|unique:idiomas',
            'nombre' => 'required|string|max:255'
        ]);

        \$idioma = Idioma::create(\$validated);

        if (\$request->expectsJson()) {
            return response()->json(\$idioma, 201);
        }

        return redirect()->route('idiomas.index', ['current_team' => \$current_team]);
    }

    public function show(Request \$request, string \$current_team, Idioma \$idioma): Response
    {
        \$team = \$this->currentTeam(\$request);
        Gate::authorize('view', [\$idioma, \$team]);

        if (\$request->expectsJson()) {
            return response()->json(\$idioma);
        }

        return response()->view('gamer.show', [
            'item' => \$idioma,
            'title' => 'Idioma: ' . \$idioma->nombre,
            'resource' => 'idiomas',
            'team' => \$team
        ]);
    }

    public function update(Request \$request, string \$current_team, Idioma \$idioma): Response
    {
        \$team = \$this->currentTeam(\$request);
        Gate::authorize('update', [\$idioma, \$team]);

        \$validated = \$request->validate([
            'codigo' => 'sometimes|required|string|max:5|unique:idiomas,codigo,' . \$idioma->id,
            'nombre' => 'sometimes|required|string|max:255'
        ]);

        \$idioma->update(\$validated);

        if (\$request->expectsJson()) {
            return response()->json(\$idioma);
        }

        return redirect()->route('idiomas.show', ['current_team' => \$current_team, 'idioma' => \$idioma]);
    }

    public function destroy(Request \$request, string \$current_team, Idioma \$idioma): Response
    {
        \$team = \$this->currentTeam(\$request);
        Gate::authorize('delete', [\$idioma, \$team]);

        \$idioma->delete();

        if (\$request->expectsJson()) {
            return response()->json(null, 204);
        }

        return redirect()->route('idiomas.index', ['current_team' => \$current_team]);
    }
}
PHP,

    'app/Http/Controllers/EtiquetaController.php' => <<<PHP
<?php

namespace App\Http\Controllers;

use App\Models\Etiqueta;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Gate;

class EtiquetaController extends Controller
{
    public function index(Request \$request, string \$current_team): Response
    {
        \$team = \$this->currentTeam(\$request);
        Gate::authorize('viewAny', [Etiqueta::class, \$team]);

        \$query = Etiqueta::query();
        
        if (\$search = \$request->input('search')) {
            \$query->where('nombre', 'like', "%{\$search}%");
        }
        
        \$items = \$query->paginate(15);

        if (\$request->expectsJson()) {
            return response()->json(\$items);
        }

        return response()->view('gamer.index', [
            'items' => \$items,
            'title' => 'Etiquetas',
            'resource' => 'etiquetas',
            'team' => \$team
        ]);
    }

    public function store(Request \$request, string \$current_team): Response
    {
        \$team = \$this->currentTeam(\$request);
        Gate::authorize('create', [Etiqueta::class, \$team]);

        \$validated = \$request->validate([
            'nombre' => 'required|string|max:255|unique:etiquetas'
        ]);

        \$etiqueta = Etiqueta::create(\$validated);

        if (\$request->expectsJson()) {
            return response()->json(\$etiqueta, 201);
        }

        return redirect()->route('etiquetas.index', ['current_team' => \$current_team]);
    }

    public function show(Request \$request, string \$current_team, Etiqueta \$etiqueta): Response
    {
        \$team = \$this->currentTeam(\$request);
        Gate::authorize('view', [\$etiqueta, \$team]);

        if (\$request->expectsJson()) {
            return response()->json(\$etiqueta);
        }

        return response()->view('gamer.show', [
            'item' => \$etiqueta,
            'title' => 'Etiqueta: ' . \$etiqueta->nombre,
            'resource' => 'etiquetas',
            'team' => \$team
        ]);
    }

    public function update(Request \$request, string \$current_team, Etiqueta \$etiqueta): Response
    {
        \$team = \$this->currentTeam(\$request);
        Gate::authorize('update', [\$etiqueta, \$team]);

        \$validated = \$request->validate([
            'nombre' => 'required|string|max:255|unique:etiquetas,nombre,' . \$etiqueta->id_etiqueta . ',id_etiqueta'
        ]);

        \$etiqueta->update(\$validated);

        if (\$request->expectsJson()) {
            return response()->json(\$etiqueta);
        }

        return redirect()->route('etiquetas.show', ['current_team' => \$current_team, 'etiqueta' => \$etiqueta]);
    }

    public function destroy(Request \$request, string \$current_team, Etiqueta \$etiqueta): Response
    {
        \$team = \$this->currentTeam(\$request);
        Gate::authorize('delete', [\$etiqueta, \$team]);

        \$etiqueta->delete();

        if (\$request->expectsJson()) {
            return response()->json(null, 204);
        }

        return redirect()->route('etiquetas.index', ['current_team' => \$current_team]);
    }
}
PHP,

    'app/Http/Controllers/ReporteModeracionController.php' => <<<PHP
<?php

namespace App\Http\Controllers;

use App\Models\ReporteModeracion;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Gate;

class ReporteModeracionController extends Controller
{
    public function index(Request \$request, string \$current_team): Response
    {
        \$team = \$this->currentTeam(\$request);
        Gate::authorize('viewAny', [ReporteModeracion::class, \$team]);

        \$items = ReporteModeracion::with(['reportador', 'reportado'])
            ->orderBy('fecha', 'desc')
            ->paginate(15);

        if (\$request->expectsJson()) {
            return response()->json(\$items);
        }

        return response()->view('gamer.index', [
            'items' => \$items,
            'title' => 'Reportes de Moderación',
            'resource' => 'reportes-moderacion',
            'team' => \$team
        ]);
    }

    public function store(Request \$request, string \$current_team): Response
    {
        \$team = \$this->currentTeam(\$request);
        Gate::authorize('create', [ReporteModeracion::class, \$team]);

        \$validated = \$request->validate([
            'id_reportado' => 'required|integer|exists:users,id',
            'motivo' => 'required|string|max:2000',
        ]);

        \$validated['id_reportador'] = \$request->user()->id;

        \$reporte = ReporteModeracion::create(\$validated);

        if (\$request->expectsJson()) {
            return response()->json(\$reporte, 201);
        }

        return redirect()->route('reportes-moderacion.index', ['current_team' => \$current_team]);
    }

    public function show(Request \$request, string \$current_team, ReporteModeracion \$reporte): Response
    {
        \$team = \$this->currentTeam(\$request);
        Gate::authorize('view', [\$reporte, \$team]);

        \$reporte->load(['reportador', 'reportado']);

        if (\$request->expectsJson()) {
            return response()->json(\$reporte);
        }

        return response()->view('gamer.show', [
            'item' => \$reporte,
            'title' => 'Reporte Moderación #' . \$reporte->id_reporte,
            'resource' => 'reportes-moderacion',
            'team' => \$team
        ]);
    }

    public function updateEstado(Request \$request, string \$current_team, ReporteModeracion \$reporte): Response
    {
        \$team = \$this->currentTeam(\$request);
        Gate::authorize('updateEstado', [\$reporte, \$team]);

        \$validated = \$request->validate([
            'estado' => 'required|in:revisado,rechazado'
        ]);

        \$reporte->update(\$validated);

        if (\$request->expectsJson()) {
            return response()->json(\$reporte);
        }

        return redirect()->route('reportes-moderacion.show', ['current_team' => \$current_team, 'reporte' => \$reporte]);
    }
}
PHP,

    'app/Http/Controllers/ReporteSoporteController.php' => <<<PHP
<?php

namespace App\Http\Controllers;

use App\Models\ReporteSoporte;
use App\Models\RespuestaSoporte;
use App\Models\VotoSoporte;
use App\Enums\TeamRole;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Gate;

class ReporteSoporteController extends Controller
{
    public function index(Request \$request, string \$current_team): Response
    {
        \$team = \$this->currentTeam(\$request);
        Gate::authorize('viewAny', [ReporteSoporte::class, \$team]);

        \$query = ReporteSoporte::with(['usuario', 'etiquetas']);
        
        \$isAdmin = \$request->user()->teamRole(\$team)?->isAtLeast(TeamRole::Admin) === true;
        
        if (!\$isAdmin) {
            \$query->where('id_usuario', \$request->user()->id);
        }

        \$items = \$query->orderBy('fecha_creacion', 'desc')->paginate(15);

        if (\$request->expectsJson()) {
            return response()->json(\$items);
        }

        return response()->view('gamer.index', [
            'items' => \$items,
            'title' => 'Reportes de Soporte',
            'resource' => 'reportes-soporte',
            'team' => \$team
        ]);
    }

    public function store(Request \$request, string \$current_team): Response
    {
        \$team = \$this->currentTeam(\$request);
        Gate::authorize('create', [ReporteSoporte::class, \$team]);

        \$validated = \$request->validate([
            'asunto' => 'required|string|max:150',
            'descripcion' => 'required|string|max:5000',
            'etiquetas' => 'nullable|array',
            'etiquetas.*' => 'integer|exists:etiquetas,id_etiqueta',
        ]);

        \$validated['id_usuario'] = \$request->user()->id;

        \$reporte = ReporteSoporte::create(\$validated);

        if (isset(\$validated['etiquetas'])) {
            \$reporte->etiquetas()->sync(\$validated['etiquetas']);
        }

        if (\$request->expectsJson()) {
            return response()->json(\$reporte, 201);
        }

        return redirect()->route('reportes-soporte.index', ['current_team' => \$current_team]);
    }

    public function show(Request \$request, string \$current_team, ReporteSoporte \$reporte): Response
    {
        \$team = \$this->currentTeam(\$request);
        Gate::authorize('view', [\$reporte, \$team]);

        \$reporte->load(['usuario', 'respuestas.usuario', 'etiquetas']);

        if (\$request->expectsJson()) {
            return response()->json(\$reporte);
        }

        return response()->view('gamer.show', [
            'item' => \$reporte,
            'title' => 'Soporte: ' . \$reporte->asunto,
            'resource' => 'reportes-soporte',
            'team' => \$team
        ]);
    }

    public function updateEstado(Request \$request, string \$current_team, ReporteSoporte \$reporte): Response
    {
        \$team = \$this->currentTeam(\$request);
        Gate::authorize('update', [\$reporte, \$team]);

        \$validated = \$request->validate([
            'estado' => 'required|in:abierto,en_proceso,cerrado'
        ]);

        \$reporte->update(\$validated);

        if (\$request->expectsJson()) {
            return response()->json(\$reporte);
        }

        return redirect()->route('reportes-soporte.show', ['current_team' => \$current_team, 'reporte' => \$reporte]);
    }

    public function responder(Request \$request, string \$current_team, ReporteSoporte \$reporte): Response
    {
        \$team = \$this->currentTeam(\$request);
        Gate::authorize('view', [\$reporte, \$team]); // Si puede ver, puede responder

        \$validated = \$request->validate([
            'mensaje' => 'required|string|max:5000'
        ]);

        \$respuesta = RespuestaSoporte::create([
            'id_reporte' => \$reporte->id_reporte,
            'id_usuario' => \$request->user()->id,
            'mensaje' => \$validated['mensaje']
        ]);

        if (\$request->expectsJson()) {
            return response()->json(\$respuesta, 201);
        }

        return redirect()->route('reportes-soporte.show', ['current_team' => \$current_team, 'reporte' => \$reporte]);
    }

    public function votar(Request \$request, string \$current_team, ReporteSoporte \$reporte): Response
    {
        \$team = \$this->currentTeam(\$request);
        Gate::authorize('view', [\$reporte, \$team]);

        \$validated = \$request->validate([
            'voto' => 'required|in:-1,1'
        ]);

        \$voto = VotoSoporte::updateOrCreate(
            ['id_reporte' => \$reporte->id_reporte, 'id_usuario' => \$request->user()->id],
            ['voto' => \$validated['voto']]
        );

        if (\$request->expectsJson()) {
            return response()->json(\$voto);
        }

        return redirect()->route('reportes-soporte.show', ['current_team' => \$current_team, 'reporte' => \$reporte]);
    }
}
PHP
];

foreach (\$files as \$path => \$content) {
    \$fullPath = \$baseDir . DIRECTORY_SEPARATOR . \$path;
    \$dir = dirname(\$fullPath);
    
    if (!is_dir(\$dir)) {
        mkdir(\$dir, 0777, true);
    }
    
    file_put_contents(\$fullPath, \$content);
    echo "Created: \$path\n";
}
?>
