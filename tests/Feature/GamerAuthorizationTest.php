<?php

namespace Tests\Feature;

use App\Enums\TeamRole;
use App\Models\Comunidad;
use App\Models\ReporteModeracion;
use App\Models\ReporteSoporte;
use App\Models\noticias;
use App\Models\Team;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class GamerAuthorizationTest extends TestCase
{
    use RefreshDatabase;

    public function test_news_listing_is_isolated_by_team(): void
    {
        [$user, $team] = $this->teamWithMember();
        $otherTeam = Team::factory()->create();

        noticias::create(['team_id' => $team->id, 'user_id' => $user->id, 'titulo' => 'Visible', 'contenido' => 'A']);
        noticias::create(['team_id' => $otherTeam->id, 'user_id' => $user->id, 'titulo' => 'Oculta', 'contenido' => 'B']);

        $response = $this->actingAs($user)->getJson(route('noticias.index', $team->slug));

        $response->assertOk()->assertJsonFragment(['titulo' => 'Visible'])->assertJsonMissing(['titulo' => 'Oculta']);
    }

    public function test_official_news_is_visible_to_members_of_any_team(): void
    {
        [$user, $team] = $this->teamWithMember();
        $otherTeam = Team::factory()->create();

        noticias::create([
            'titulo' => 'Actualización oficial',
            'contenido' => 'Novedades publicadas por el estudio.',
            'fuente_nombre' => 'PlayStation Blog',
            'fuente_url' => 'https://blog.playstation.com/noticia',
            'es_oficial' => true,
        ]);

        $response = $this->actingAs($user)->getJson(route('noticias.index', $team->slug));

        $response->assertOk()->assertJsonFragment(['titulo' => 'Actualización oficial']);
        $this->assertTrue($user->belongsToTeam($team));
        $this->assertNotSame($team->id, $otherTeam->id);
    }

    public function test_official_news_sync_imports_and_deduplicates_feed_items(): void
    {
        Http::fake([
            '*' => Http::response(<<<'XML'
                <rss><channel><item><title>Parche oficial</title><link>https://example.com/parche</link><description>Detalles del parche.</description></item></channel></rss>
                XML, 200),
        ]);

        Artisan::call('news:sync-official', ['--limit' => 1]);
        Artisan::call('news:sync-official', ['--limit' => 1]);

        $this->assertDatabaseCount('noticias', 1);
        $this->assertDatabaseHas('noticias', [
            'titulo' => 'Parche oficial',
            'es_oficial' => true,
            'team_id' => null,
        ]);
    }

    public function test_member_cannot_delete_another_members_news(): void
    {
        [$user, $team] = $this->teamWithMember();
        $author = User::factory()->create(['email_verified_at' => now()]);
        $team->members()->attach($author, ['role' => TeamRole::Member]);
        $noticia = noticias::create(['team_id' => $team->id, 'user_id' => $author->id, 'titulo' => 'News', 'contenido' => 'Text']);

        $this->actingAs($user)
            ->deleteJson(route('noticias.destroy', [$team->slug, $noticia]))
            ->assertForbidden();

        $this->assertDatabaseHas('noticias', ['id' => $noticia->id]);
    }

    public function test_news_can_be_created_with_an_uploaded_image(): void
    {
        [$user, $team] = $this->teamWithMember();
        Storage::fake('public');

        $response = $this->actingAs($user)->post(route('noticias.store', $team->slug), [
            'titulo' => 'Noticia con imagen',
            'contenido' => 'Contenido de prueba.',
            'imagen' => UploadedFile::fake()->create('noticia.png', 100, 'image/png'),
        ]);

        $response->assertRedirect();
        $noticia = noticias::query()->where('titulo', 'Noticia con imagen')->firstOrFail();

        Storage::disk('public')->assertExists($noticia->imagen_url);
        $this->assertStringStartsWith('noticias/', $noticia->imagen_url);
    }

    public function test_created_news_appears_in_the_html_listing(): void
    {
        [$user, $team] = $this->teamWithMember();

        $this->actingAs($user)->post(route('noticias.store', $team->slug), [
            'titulo' => 'Noticia visible en el listado',
            'contenido' => 'Esta publicación debe permanecer disponible.',
        ])->assertRedirect();

        $this->actingAs($user)
            ->get(route('noticias.index', $team->slug))
            ->assertOk()
            ->assertSee('Noticia visible en el listado')
            ->assertSee('Esta publicación debe permanecer disponible.');
    }

    public function test_news_can_be_created_with_an_image_url(): void
    {
        [$user, $team] = $this->teamWithMember();
        $imageUrl = 'https://example.com/images/noticia.png';

        $response = $this->actingAs($user)->post(route('noticias.store', $team->slug), [
            'titulo' => 'Noticia con enlace',
            'contenido' => 'Contenido con imagen externa.',
            'imagen_url' => $imageUrl,
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('noticias', [
            'titulo' => 'Noticia con enlace',
            'imagen_url' => $imageUrl,
        ]);
    }

    public function test_comments_always_use_the_authenticated_user(): void
    {
        [$user, $team] = $this->teamWithMember();
        $otherUser = User::factory()->create();
        $publicationId = DB::table('publicaciones')->insertGetId([
            'user_id' => $user->id,
            'team_id' => $team->id,
            'titulo' => 'Post',
            'contenido' => 'Contenido',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $response = $this->actingAs($user)->postJson(route('comentarios.store', $team->slug), [
            'id_publicacion' => $publicationId,
            'id_usuario' => $otherUser->id,
            'contenido' => 'Comentario seguro',
        ]);

        $response->assertCreated();
        $this->assertDatabaseHas('comentarios', [
            'id_usuario' => $user->id,
            'contenido' => 'Comentario seguro',
            'team_id' => $team->id,
        ]);
        $this->assertDatabaseMissing('comentarios', ['id_usuario' => $otherUser->id]);
    }

    public function test_a_community_is_created_in_the_current_team_and_adds_creator(): void
    {
        [$user, $team] = $this->teamWithMember();

        $response = $this->actingAs($user)->postJson(route('comunidad.store', $team->slug), [
            'nombre' => 'Arena competitiva',
            'descripcion' => 'Torneos y partidas.',
        ]);

        $response->assertCreated();
        $community = Comunidad::query()->firstOrFail();

        $this->assertSame($team->id, $community->team_id);
        $this->assertDatabaseHas('miembros_comunidad', ['comunidad_id' => $community->id, 'user_id' => $user->id]);
    }

    public function test_support_reports_are_isolated_by_team(): void
    {
        [$user, $team] = $this->teamWithMember();
        $otherTeam = Team::factory()->create();

        $visible = ReporteSoporte::factory()->create(['team_id' => $team->id]);
        $hidden = ReporteSoporte::factory()->create(['team_id' => $otherTeam->id]);

        $response = $this->actingAs($user)->getJson(route('reportes-soporte.index', $team->slug));

        $response->assertOk()
            ->assertJsonFragment(['id_reporte' => $visible->id_reporte])
            ->assertJsonMissing(['id_reporte' => $hidden->id_reporte]);
    }

    public function test_support_report_from_another_team_cannot_be_viewed(): void
    {
        [$user, $team] = $this->teamWithMember();
        $otherTeam = Team::factory()->create();
        $report = ReporteSoporte::factory()->create(['team_id' => $otherTeam->id]);

        $this->actingAs($user)
            ->getJson(route('reportes-soporte.show', [$team->slug, $report]))
            ->assertForbidden();
    }

    public function test_moderation_report_from_another_team_cannot_be_viewed(): void
    {
        [$user, $team] = $this->teamWithMember();
        $otherTeam = Team::factory()->create();
        $report = ReporteModeracion::create([
            'team_id' => $otherTeam->id,
            'id_reportador' => $user->id,
            'motivo' => 'Contenido inapropiado',
            'fecha' => now(),
        ]);

        $this->actingAs($user)
            ->getJson(route('reportes-moderacion.show', [$team->slug, $report]))
            ->assertForbidden();
    }

    private function teamWithMember(): array
    {
        $user = User::factory()->create(['email_verified_at' => now()]);
        $team = Team::factory()->create();
        $team->members()->attach($user, ['role' => TeamRole::Member]);

        return [$user, $team];
    }
}
