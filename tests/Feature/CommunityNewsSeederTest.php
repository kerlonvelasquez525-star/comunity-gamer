<?php

namespace Tests\Feature;

use App\Models\noticias;
use Database\Seeders\CommunityNewsSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CommunityNewsSeederTest extends TestCase
{
    use RefreshDatabase;

    public function test_editorial_news_and_original_artwork_are_available_in_the_public_feed(): void
    {
        $this->seed(CommunityNewsSeeder::class);
        $this->seed(CommunityNewsSeeder::class);

        $articles = noticias::query()
            ->whereNull('team_id')
            ->where('es_oficial', true)
            ->get();

        $this->assertCount(7, $articles);

        foreach ($articles as $article) {
            $this->assertNotEmpty($article->imagen_url);
            $this->assertFileExists(public_path(ltrim($article->imagen_url, '/')));
        }

        $this->get(route('public-noticias.index', ['page' => 2]))
            ->assertOk()
            ->assertSee('Perú vs. México: cuando la rivalidad llega al mando')
            ->assertSee('/images/community-news/duelo-de-comunidades.svg');

        $this->get(route('home'))
            ->assertOk()
            ->assertSee('La nostalgia también suma puntos')
            ->assertSee('/images/community-news/nostalgia-retro.svg');
    }
}
