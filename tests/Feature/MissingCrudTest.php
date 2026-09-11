<?php

namespace Tests\Feature;

use App\Enums\TeamRole;
use App\Models\Juego;
use App\Models\Publicacion;
use App\Models\Team;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MissingCrudTest extends TestCase
{
    use RefreshDatabase;

    public function test_publications_are_created_inside_the_current_team(): void
    {
        [$user, $team] = $this->teamWithMember();

        $response = $this->actingAs($user)->postJson(route('publicaciones.store', $team->slug), [
            'titulo' => 'Publicacion de prueba',
            'contenido' => 'Contenido publicado en el equipo.',
        ]);

        $response->assertCreated();
        $this->assertDatabaseHas('publicaciones', [
            'team_id' => $team->id,
            'user_id' => $user->id,
            'titulo' => 'Publicacion de prueba',
        ]);
    }

    public function test_publication_listing_does_not_cross_team_boundaries(): void
    {
        [$user, $team] = $this->teamWithMember();
        $otherTeam = Team::factory()->create();

        Publicacion::create(['team_id' => $team->id, 'user_id' => $user->id, 'titulo' => 'Visible', 'contenido' => 'A']);
        Publicacion::create(['team_id' => $otherTeam->id, 'user_id' => $user->id, 'titulo' => 'Oculta', 'contenido' => 'B']);

        $this->actingAs($user)
            ->getJson(route('publicaciones.index', $team->slug))
            ->assertOk()
            ->assertJsonFragment(['titulo' => 'Visible'])
            ->assertJsonMissing(['titulo' => 'Oculta']);
    }

    public function test_catalog_game_can_be_created_and_returned_with_json(): void
    {
        [$user, $team] = $this->teamWithMember(TeamRole::Admin);

        $response = $this->actingAs($user)->postJson(route('juegos.store', $team->slug), [
            'titulo' => 'Juego de prueba',
            'descripcion' => 'Descripcion del juego.',
        ]);

        $response->assertCreated()->assertJsonFragment(['titulo' => 'Juego de prueba']);
        $this->assertDatabaseHas('juegos', ['titulo' => 'Juego de prueba']);
        $this->assertTrue(Juego::query()->where('titulo', 'Juego de prueba')->exists());
    }

    public function test_friendship_requests_are_limited_to_the_authenticated_user(): void
    {
        [$user] = $this->teamWithMember();
        $friend = User::factory()->create();

        $this->actingAs($user)->postJson(route('amistades.store'), ['amigo_id' => $friend->id])->assertCreated();

        $this->assertDatabaseHas('amistades', [
            'user_id' => $user->id,
            'amigo_id' => $friend->id,
            'estado' => 'pendiente',
        ]);
    }

    private function teamWithMember(TeamRole $role = TeamRole::Member): array
    {
        $user = User::factory()->create(['email_verified_at' => now()]);
        $team = Team::factory()->create();
        $team->members()->attach($user, ['role' => $role->value]);

        return [$user, $team];
    }
}
