<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class ChatbotTest extends TestCase
{
    use RefreshDatabase;

    public function test_verified_users_can_open_the_chatbot_page(): void
    {
        $user = User::factory()->create(['email_verified_at' => now()]);

        $this->actingAs($user)
            ->get(route('chatbot.index'))
            ->assertOk()
            ->assertSee('Asistente de la comunidad')
            ->assertSee('assistantQuestion');
    }

    public function test_authenticated_users_receive_predefined_privacy_guidance(): void
    {
        $user = User::factory()->create(['email_verified_at' => now()]);

        $this->actingAs($user)
            ->postJson(route('chat.bot.answer'), ['question' => 'Como protegeis mi privacidad?'])
            ->assertOk()
            ->assertJsonPath('intent', 'privacy')
            ->assertJsonStructure(['intent', 'answer', 'suggestions']);
    }

    public function test_chatbot_reports_the_real_count_of_active_authenticated_users(): void
    {
        $firstUser = User::factory()->create(['email_verified_at' => now()]);
        $secondUser = User::factory()->create(['email_verified_at' => now()]);
        $staleUser = User::factory()->create(['email_verified_at' => now()]);

        DB::table('sessions')->insert([
            ['id' => 'first-browser', 'user_id' => $firstUser->id, 'payload' => 'active', 'last_activity' => now()->timestamp, 'ip_address' => null, 'user_agent' => null],
            ['id' => 'first-mobile', 'user_id' => $firstUser->id, 'payload' => 'active', 'last_activity' => now()->timestamp, 'ip_address' => null, 'user_agent' => null],
            ['id' => 'second-browser', 'user_id' => $secondUser->id, 'payload' => 'active', 'last_activity' => now()->timestamp, 'ip_address' => null, 'user_agent' => null],
            ['id' => 'stale-browser', 'user_id' => $staleUser->id, 'payload' => 'stale', 'last_activity' => now()->subMinutes(121)->timestamp, 'ip_address' => null, 'user_agent' => null],
            ['id' => 'guest-browser', 'user_id' => null, 'payload' => 'guest', 'last_activity' => now()->timestamp, 'ip_address' => null, 'user_agent' => null],
        ]);

        $this->actingAs($firstUser)
            ->postJson(route('chat.bot.answer'), ['question' => 'Cuantos usuarios hay en linea?'])
            ->assertOk()
            ->assertJsonPath('intent', 'online_users')
            ->assertJsonPath('answer', 'Ahora mismo hay 2 usuarios en línea. La cifra incluye usuarios autenticados con actividad dentro de la ventana activa de la plataforma.');
    }

    public function test_chatbot_matches_natural_report_questions_to_moderation_guidance(): void
    {
        $user = User::factory()->create(['email_verified_at' => now()]);

        $this->actingAs($user)
            ->postJson(route('chat.bot.answer'), ['question' => '¿Cómo reporto un mensaje de acoso?'])
            ->assertOk()
            ->assertJsonPath('intent', 'report')
            ->assertJsonPath('suggestions.0', '¿Qué puedo reportar?');
    }

    public function test_chatbot_does_not_persist_questions_as_private_messages(): void
    {
        $user = User::factory()->create(['email_verified_at' => now()]);

        $this->actingAs($user)->postJson(route('chat.bot.answer'), ['question' => 'Como funciona 2FA?'])->assertOk();

        $this->assertDatabaseCount('mensajes_privados', 0);
    }

    public function test_chatbot_requires_authentication_and_question_validation(): void
    {
        $this->postJson(route('chat.bot.answer'), ['question' => 'privacidad'])->assertUnauthorized();

        $user = User::factory()->create(['email_verified_at' => now()]);

        $this->actingAs($user)->postJson(route('chat.bot.answer'), ['question' => ''])->assertUnprocessable();
    }
}
