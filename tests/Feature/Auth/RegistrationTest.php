<?php

namespace Tests\Feature\Auth;

use App\Models\Comunidad;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RegistrationTest extends TestCase
{
    use RefreshDatabase;

    public function test_registration_screen_can_be_rendered(): void
    {
        $response = $this->get(route('register'));

        $response->assertOk();
    }

    public function test_new_users_can_register(): void
    {
        $response = $this->post(route('register.store'), [
            'name' => 'John Doe',
            'email' => 'test@example.com',
            'password' => 'password',
            'password_confirmation' => 'password',
        ]);

        $user = User::where('email', 'test@example.com')->first();

        $response->assertSessionHasNoErrors()
            ->assertRedirect(route('dashboard', absolute: false));

        $this->assertAuthenticated();
    }

    public function test_registration_shows_open_public_communities_for_optional_requests(): void
    {
        Comunidad::create([
            'nombre' => 'Comunidad abierta',
            'tipo' => 'publica',
            'estado' => 'Abierta',
        ]);
        Comunidad::create([
            'nombre' => 'Comunidad privada',
            'tipo' => 'privada',
            'estado' => 'Abierta',
        ]);

        $this->get(route('register'))
            ->assertOk()
            ->assertSee('Comunidad abierta')
            ->assertDontSee('Comunidad privada')
            ->assertSee('comunidades[]');
    }

    public function test_new_user_can_request_multiple_communities_without_joining_them(): void
    {
        $firstCommunity = Comunidad::create([
            'nombre' => 'Primera comunidad',
            'tipo' => 'publica',
            'estado' => 'Abierta',
        ]);
        $secondCommunity = Comunidad::create([
            'nombre' => 'Segunda comunidad',
            'tipo' => 'publica',
            'estado' => 'Abierta',
        ]);

        $response = $this->post(route('register.store'), [
            'name' => 'New Gamer',
            'email' => 'new-gamer@example.com',
            'password' => 'password',
            'password_confirmation' => 'password',
            'comunidades' => [$firstCommunity->id, $secondCommunity->id],
        ]);

        $user = User::where('email', 'new-gamer@example.com')->firstOrFail();

        $response->assertSessionHasNoErrors()
            ->assertRedirect(route('dashboard', absolute: false));

        $this->assertDatabaseHas('comunidad_solicitudes', [
            'comunidad_id' => $firstCommunity->id,
            'user_id' => $user->id,
            'estado' => 'pendiente',
        ]);
        $this->assertDatabaseHas('comunidad_solicitudes', [
            'comunidad_id' => $secondCommunity->id,
            'user_id' => $user->id,
            'estado' => 'pendiente',
        ]);
        $this->assertDatabaseMissing('miembros_comunidad', [
            'comunidad_id' => $firstCommunity->id,
            'user_id' => $user->id,
        ]);
        $this->assertDatabaseMissing('miembros_comunidad', [
            'comunidad_id' => $secondCommunity->id,
            'user_id' => $user->id,
        ]);
    }

    public function test_registration_rejects_requests_for_non_public_communities(): void
    {
        $community = Comunidad::create([
            'nombre' => 'Comunidad cerrada',
            'tipo' => 'privada',
            'estado' => 'Abierta',
        ]);

        $this->post(route('register.store'), [
            'name' => 'New Gamer',
            'email' => 'new-gamer@example.com',
            'password' => 'password',
            'password_confirmation' => 'password',
            'comunidades' => [$community->id],
        ])->assertSessionHasErrors('comunidades.0');

        $this->assertDatabaseMissing('users', ['email' => 'new-gamer@example.com']);
        $this->assertDatabaseMissing('comunidad_solicitudes', ['comunidad_id' => $community->id]);
    }

    public function test_registration_does_not_accept_requests_for_full_communities(): void
    {
        $community = Comunidad::create([
            'nombre' => 'Comunidad completa',
            'tipo' => 'publica',
            'estado' => 'Abierta',
            'max_miembros' => 1,
        ]);
        $community->miembros()->attach(User::factory()->create()->id, ['rol' => 'miembro']);

        $this->get(route('register'))
            ->assertOk()
            ->assertDontSee('Comunidad completa');

        $this->post(route('register.store'), [
            'name' => 'New Gamer',
            'email' => 'new-gamer@example.com',
            'password' => 'password',
            'password_confirmation' => 'password',
            'comunidades' => [$community->id],
        ])->assertSessionHasErrors('comunidades');

        $this->assertDatabaseMissing('users', ['email' => 'new-gamer@example.com']);
        $this->assertDatabaseMissing('comunidad_solicitudes', ['comunidad_id' => $community->id]);
    }
}
