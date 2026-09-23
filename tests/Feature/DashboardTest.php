<?php

namespace Tests\Feature;

use App\Models\Comunidad;
use App\Models\noticias;
use App\Models\Problemas;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DashboardTest extends TestCase
{
    use RefreshDatabase;

    public function test_guests_are_redirected_to_the_login_page(): void
    {
        $user = User::factory()->create();
        $team = $user->currentTeam;

        $response = $this->get(route('dashboard'));
        $response->assertRedirect(route('login'));
    }

    public function test_authenticated_users_can_visit_the_dashboard(): void
    {
        $user = User::factory()->create();
        $team = $user->currentTeam;

        $response = $this
            ->actingAs($user)
            ->get(route('dashboard'));

        $response->assertOk();
    }

    public function test_dashboard_metrics_are_loaded_from_database(): void
    {
        $user = User::factory()->create();
        $team = $user->currentTeam;
        $otherUser = User::factory()->create();
        $otherTeam = $otherUser->currentTeam;

        Comunidad::factory()->create(['team_id' => $team->id]);
        Comunidad::factory()->count(2)->create(['team_id' => $otherTeam->id]);
        noticias::factory()->create(['team_id' => $team->id, 'user_id' => $user->id]);
        noticias::factory()->create(['team_id' => $otherTeam->id, 'user_id' => $otherUser->id]);
        Problemas::factory()->create([
            'team_id' => $team->id,
            'user_id' => $user->id,
            'titulo' => 'Problema del equipo actual',
            'descripcion' => 'Descripción del problema del equipo actual.',
            'estado' => 'abierto',
        ]);
        Problemas::factory()->create([
            'team_id' => $otherTeam->id,
            'user_id' => $otherUser->id,
            'titulo' => 'Problema de otro equipo',
            'descripcion' => 'Descripción del problema de otro equipo.',
            'estado' => 'abierto',
        ]);

        $expectedNews = noticias::query()
            ->where(function ($query) use ($team): void {
                $query->where('team_id', $team->id)
                    ->orWhere(function ($publicQuery): void {
                        $publicQuery->whereNull('team_id')
                            ->where('es_oficial', true);
                    });
            })
            ->count();

        $response = $this
            ->actingAs($user)
            ->getJson(route('dashboard', $team->slug));

        $response
            ->assertOk()
            ->assertJsonPath('comunidades_totales', 3)
            ->assertJsonPath('noticias_totales', $expectedNews)
            ->assertJsonPath('problemas_abiertos', 2);
    }
}
