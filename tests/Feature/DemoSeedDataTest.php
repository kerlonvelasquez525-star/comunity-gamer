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
}
