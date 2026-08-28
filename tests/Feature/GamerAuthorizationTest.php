<?php

namespace Tests\Feature;

use App\Enums\TeamRole;
use App\Models\Comunidad;
use App\Models\noticias;
use App\Models\Team;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
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

    private function teamWithMember(): array
    {
        $user = User::factory()->create(['email_verified_at' => now()]);
        $team = Team::factory()->create();
        $team->members()->attach($user, ['role' => TeamRole::Member]);

        return [$user, $team];
    }
}
