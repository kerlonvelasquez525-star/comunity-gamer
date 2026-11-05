<?php

namespace App\Actions\Fortify;

use App\Actions\Teams\CreateTeam;
use App\Concerns\PasswordValidationRules;
use App\Concerns\ProfileValidationRules;
use App\Models\Comunidad;
use App\Models\ComunidadSolicitud;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Laravel\Fortify\Contracts\CreatesNewUsers;

class CreateNewUser implements CreatesNewUsers
{
    use PasswordValidationRules, ProfileValidationRules;

    public function __construct(private CreateTeam $createTeam)
    {
        //
    }

    /**
     * Validate and create a newly registered user.
     *
     * @param  array<string, mixed>  $input
     */
    public function create(array $input): User
    {
        Validator::make($input, [
            ...$this->profileRules(),
            'password' => $this->passwordRules(),
            'comunidades' => ['sometimes', 'array'],
            'comunidades.*' => [
                'integer',
                'distinct',
                Rule::exists('comunidades', 'id')
                    ->where('tipo', 'publica')
                    ->where('estado', 'Abierta')
                    ->whereNull('deleted_at'),
            ],
        ])->validate();

        return DB::transaction(function () use ($input): User {
            $communityIds = $input['comunidades'] ?? [];
            $communities = Comunidad::query()
                ->whereKey($communityIds)
                ->withCount('miembros')
                ->orderBy('id')
                ->lockForUpdate()
                ->get();

            foreach ($communities as $community) {
                if ($community->tipo !== 'publica' || $community->estado !== 'Abierta') {
                    throw ValidationException::withMessages([
                        'comunidades' => 'Una de las comunidades seleccionadas ya no acepta solicitudes.',
                    ]);
                }

                if ($community->max_miembros && $community->miembros_count >= $community->max_miembros) {
                    throw ValidationException::withMessages([
                        'comunidades' => "La comunidad {$community->nombre} ya alcanzó su capacidad máxima.",
                    ]);
                }
            }

            $user = User::create([
                'name' => $input['name'],
                'email' => $input['email'],
                'password' => $input['password'],
            ]);

            $this->createTeam->handle($user, $user->name."'s Team", isPersonal: true);

            foreach ($communities as $community) {
                ComunidadSolicitud::create([
                    'comunidad_id' => $community->id,
                    'user_id' => $user->id,
                    'estado' => 'pendiente',
                ]);
            }

            return $user;
        });
    }
}
