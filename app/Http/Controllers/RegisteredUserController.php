<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Enums\TeamRole;
use App\Models\Team;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class RegisteredUserController extends Controller
{
    /**
     * Muestra la vista de registro.
     */
    public function create(): View
    {
        return view('pages.auth.register');
    }

    /**
     * Procesa la solicitud de registro entrante.
     *
     * @throws ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:'.User::class],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        $demoTeam = Team::query()->where('slug', 'nexus-demo-community')->first();

        if ($demoTeam) {
            $demoTeam->members()->syncWithoutDetaching([
                $user->id => ['role' => TeamRole::Member->value],
            ]);
            $user->forceFill(['current_team_id' => $demoTeam->id])->save();
        }

        event(new Registered($user));

        Auth::login($user);

        // Si usas lógica de equipos y el usuario no tiene equipo inicial:
        if (! $user->currentTeam) {
            return redirect()->route('teams.index');
        }

        return redirect()->route('dashboard', ['team' => $user->currentTeam->slug]);
    }
}
