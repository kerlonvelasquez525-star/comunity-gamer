<?php

namespace Tests\Feature;

use App\Models\NoticiasComentario;
use App\Models\User;
use App\Models\noticias;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DemoSeedDataTest extends TestCase
{
    use RefreshDatabase;

    public function test_database_seed_creates_a_realistic_demo_dataset(): void
    {
        $this->artisan('db:seed')->assertSuccessful();

        $this->assertGreaterThanOrEqual(30, User::count());
        $this->assertGreaterThanOrEqual(30, noticias::query()->whereNull('team_id')->where('es_oficial', true)->count());
        $this->assertGreaterThanOrEqual(60, NoticiasComentario::count());
    }

    public function test_public_homepage_uses_demo_user_statistics(): void
    {
        $this->artisan('db:seed')->assertSuccessful();

        $usersCount = User::count();
        $officialNewsCount = noticias::query()->whereNull('team_id')->where('es_oficial', true)->count();
        $verifiedUsers = User::query()->whereNotNull('email_verified_at')->count();
        $telemetry = $usersCount > 0 ? round(($verifiedUsers / $usersCount) * 100, 1) : 0;

        $this->get('/')->assertOk()
            ->assertSee((string) $usersCount)
            ->assertSee((string) $officialNewsCount)
            ->assertSee((string) $telemetry.'%');
    }

    public function test_public_community_page_displays_database_community_cards(): void
    {
        $this->artisan('db:seed')->assertSuccessful();

        $homepage = $this->get('/');

        $homepage->assertOk()
            ->assertDontSee('Zona de Squad')
            ->assertDontSee('Estrategia y Guias')
            ->assertDontSee('Torneos Nexus')
            ->assertSee('FUENTES VERIFICADAS // ACTUALIZACIONES')
            ->assertSee('Noticias oficiales de videojuegos')
            ->assertSee('Novedades publicadas por los blogs oficiales de las principales plataformas.');

        $this->get(route('public-comunidad.index'))->assertOk()
            ->assertSee('Zona de Squad')
            ->assertSee('Estrategia y Guias')
            ->assertSee('Torneos Nexus')
            ->assertSee('Iniciar sesión para unirse');
    }

    public function test_guest_comment_is_saved_after_login(): void
    {
        $this->artisan('db:seed')->assertSuccessful();

        $noticia = noticias::query()->whereNull('team_id')->where('es_oficial', true)->firstOrFail();
        $user = User::query()->firstOrFail();

        $this->post(route('official-news.comments.store', $noticia), [
            'contenido' => 'Comentario escrito antes de iniciar sesión.',
        ])->assertRedirect(route('login'));

        $this->actingAs($user)->get('/')->assertOk();

        $this->assertDatabaseHas('noticias_comentarios', [
            'noticia_id' => $noticia->id,
            'user_id' => $user->id,
            'contenido' => 'Comentario escrito antes de iniciar sesión.',
        ]);
    }
}
