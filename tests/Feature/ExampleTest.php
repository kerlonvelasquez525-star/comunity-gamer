<?php

namespace Tests\Feature;

use App\Enums\TeamRole;
use App\Models\noticias;
use App\Models\NoticiasComentario;
use App\Models\Team;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ExampleTest extends TestCase
{
    use RefreshDatabase;

    public function test_returns_a_successful_response(): void
    {
        $response = $this->get(route('home'));

        $response->assertOk();
    }

    public function test_guest_navigation_goes_to_public_sections_without_login(): void
    {
        $this->get(route('home'))
            ->assertOk()
            ->assertSee(route('public-noticias.index'))
            ->assertSee(route('home') . '#comentarios')
            ->assertSee(route('public-comunidad.index'))
            ->assertSee(route('public-problemas.index'));
    }

    public function test_guest_users_can_comment_public_news_without_login(): void
    {
        $news = noticias::create([
            'titulo' => 'Novedad oficial pública',
            'contenido' => 'Contenido oficial para toda la comunidad.',
            'fuente_nombre' => 'Xbox Wire',
            'fuente_url' => 'https://example.com/noticia-oficial',
            'es_oficial' => true,
        ]);

        $this->post(route('official-news.comments.store', $news), ['contenido' => 'Comentario de visitante.'])
            ->assertRedirect();

        $this->assertDatabaseHas('noticias_comentarios', [
            'noticia_id' => $news->id,
            'user_id' => null,
            'contenido' => 'Comentario de visitante.',
        ]);
    }

    public function test_guest_users_can_submit_public_news_comment_via_ajax(): void
    {
        $news = noticias::create([
            'titulo' => 'Novedad oficial pública',
            'contenido' => 'Contenido oficial para toda la comunidad.',
            'fuente_nombre' => 'Xbox Wire',
            'fuente_url' => 'https://example.com/noticia-oficial',
            'es_oficial' => true,
        ]);

        $this->postJson(route('official-news.comments.store', $news), ['contenido' => 'Comentario desde AJAX.'])
            ->assertCreated()
            ->assertJsonPath('comment.contenido', 'Comentario desde AJAX.');
    }

    public function test_public_news_page_is_available_without_login(): void
    {
        $this->get(route('public-noticias.index'))
            ->assertOk()
            ->assertSee('Noticias oficiales');
    }

    public function test_public_community_page_is_available_without_login(): void
    {
        $this->get(route('public-comunidad.index'))
            ->assertOk()
            ->assertSee('Comunidades activas');
    }

    public function test_public_problems_page_is_available_without_login(): void
    {
        $this->get(route('public-problemas.index'))
            ->assertOk()
            ->assertSee('Soporte y ayuda');
    }

    public function test_public_home_shows_official_news_and_accepts_comments(): void
    {
        $news = noticias::create([
            'titulo' => 'Novedad oficial pública',
            'contenido' => 'Contenido oficial para toda la comunidad.',
            'fuente_nombre' => 'Xbox Wire',
            'fuente_url' => 'https://example.com/noticia-oficial',
            'es_oficial' => true,
        ]);
        $user = User::factory()->create(['email_verified_at' => now()]);

        $this->get(route('home'))
            ->assertOk()
            ->assertSee('Novedad oficial pública');

        $this->actingAs($user)
            ->post(route('official-news.comments.store', $news), ['contenido' => 'Excelente actualización.'])
            ->assertRedirect();

        $this->assertDatabaseHas('noticias_comentarios', [
            'noticia_id' => $news->id,
            'user_id' => $user->id,
            'contenido' => 'Excelente actualización.',
        ]);
        $this->assertInstanceOf(NoticiasComentario::class, NoticiasComentario::query()->first());
    }

    public function test_authenticated_user_can_comment_on_team_news(): void
    {
        $user = User::factory()->create(['email_verified_at' => now()]);
        $team = Team::factory()->create(['name' => 'Team Alpha']);
        $user->teams()->attach($team->id, ['role' => TeamRole::Owner->value]);
        $user->switchTeam($team);

        $news = noticias::create([
            'team_id' => $team->id,
            'user_id' => $user->id,
            'titulo' => 'Noticia del equipo',
            'contenido' => 'Contenido del equipo',
            'categoria' => 'Actualización',
        ]);

        $this->actingAs($user)
            ->post(route('noticias.comentarios.store', [$team->slug, $news]), ['contenido' => 'Comentario del equipo.'])
            ->assertRedirect();

        $this->assertDatabaseHas('noticias_comentarios', [
            'noticia_id' => $news->id,
            'user_id' => $user->id,
            'contenido' => 'Comentario del equipo.',
        ]);
    }

    public function test_user_can_create_a_team_community_with_extra_fields(): void
    {
        $user = User::factory()->create(['email_verified_at' => now()]);
        $team = Team::factory()->create(['name' => 'Team Beta']);
        $user->teams()->attach($team->id, ['role' => TeamRole::Owner->value]);
        $user->switchTeam($team);

        $this->actingAs($user)
            ->post(route('comunidad.store', $team->slug), [
                'nombre' => 'Rival Squad',
                'descripcion' => 'Comunidad para partidas competitivas.',
                'tipo' => 'publica',
                'nivel' => 'Gold',
                'rango' => 'Plata',
                'max_miembros' => 25,
                'estado' => 'Abierta',
            ])
            ->assertRedirect();

        $this->assertDatabaseHas('comunidades', [
            'team_id' => $team->id,
            'nombre' => 'Rival Squad',
            'tipo' => 'publica',
            'nivel' => 'Gold',
            'rango' => 'Plata',
            'estado' => 'Abierta',
            'max_miembros' => 25,
        ]);
    }

    public function test_owner_can_assign_roles_to_community_members(): void
    {
        $owner = User::factory()->create(['email_verified_at' => now()]);
        $member = User::factory()->create(['email_verified_at' => now()]);
        $team = Team::factory()->create(['name' => 'Team Roles']);

        $owner->teams()->attach($team->id, ['role' => TeamRole::Owner->value]);
        $member->teams()->attach($team->id, ['role' => TeamRole::Member->value]);
        $owner->switchTeam($team);

        $comunidad = \App\Models\Comunidad::create([
            'team_id' => $team->id,
            'nombre' => 'Clan de pruebas',
            'descripcion' => 'Comunidad para roles',
            'creador_id' => $owner->id,
        ]);

        $comunidad->miembros()->syncWithoutDetaching([
            $owner->id => ['rol' => 'admin'],
            $member->id => ['rol' => 'miembro'],
        ]);

        $this->actingAs($owner)
            ->patch(route('comunidad.miembros.rol', [$team->slug, $comunidad, $member]), [
                'rol' => 'moderador',
            ])
            ->assertRedirect();

        $this->assertDatabaseHas('miembros_comunidad', [
            'comunidad_id' => $comunidad->id,
            'user_id' => $member->id,
            'rol' => 'moderador',
        ]);
    }

    public function test_owner_can_invite_team_member_to_community(): void
    {
        $owner = User::factory()->create(['email_verified_at' => now()]);
        $member = User::factory()->create(['email_verified_at' => now()]);
        $team = Team::factory()->create(['name' => 'Team Invite']);
        $owner->teams()->attach($team->id, ['role' => TeamRole::Owner->value]);
        $member->teams()->attach($team->id, ['role' => TeamRole::Member->value]);
        $owner->switchTeam($team);

        $comunidad = \App\Models\Comunidad::create([
            'team_id' => $team->id,
            'nombre' => 'Clan invitado',
            'descripcion' => 'Comunidad para invitar',
            'creador_id' => $owner->id,
        ]);

        $comunidad->miembros()->syncWithoutDetaching([
            $owner->id => ['rol' => 'admin'],
        ]);

        $this->actingAs($owner)
            ->post(route('comunidad.miembros.invitar', [$team->slug, $comunidad]), [
                'user_id' => $member->id,
            ])
            ->assertRedirect();

        $this->assertDatabaseHas('miembros_comunidad', [
            'comunidad_id' => $comunidad->id,
            'user_id' => $member->id,
            'rol' => 'miembro',
        ]);
    }
}
