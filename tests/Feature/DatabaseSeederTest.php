<?php

namespace Tests\Feature;

use App\Models\NoticiasComentario;
use App\Models\Comunidad;
use App\Models\Team;
use App\Models\User;
use App\Models\noticias;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DatabaseSeederTest extends TestCase
{
    use RefreshDatabase;

    public function test_seeding_preserves_existing_official_news_and_makes_creators_community_members(): void
    {
        $author = User::factory()->create();
        $news = noticias::create([
            'team_id' => null,
            'user_id' => $author->id,
            'titulo' => 'Existing official news',
            'contenido' => 'This content belongs to an existing record.',
            'es_oficial' => true,
        ]);
        $comment = NoticiasComentario::create([
            'noticia_id' => $news->id,
            'user_id' => $author->id,
            'contenido' => 'An existing comment must remain.',
        ]);

        $this->seed(DatabaseSeeder::class);

        $this->assertDatabaseHas('noticias', ['id' => $news->id]);
        $this->assertDatabaseHas('noticias_comentarios', ['id' => $comment->id]);

        $communities = Comunidad::query()
            ->where('team_id', Team::query()->where('slug', 'nexus-demo-community')->value('id'))
            ->get();

        foreach ($communities as $community) {
            $this->assertDatabaseHas('miembros_comunidad', [
                'comunidad_id' => $community->id,
                'user_id' => $community->creador_id,
                'rol' => 'admin',
            ]);
        }
    }
}
