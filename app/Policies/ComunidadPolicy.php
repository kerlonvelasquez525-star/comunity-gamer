<?php

namespace App\Policies;

use App\Models\Comunidad;
use App\Models\Team;
use App\Models\User;

class ComunidadPolicy
{
    public function viewAny(User $user, Team $team): bool
    {
        return $user->belongsToTeam($team);
    }

    public function view(User $user, Comunidad $comunidad, Team $team): bool
    {
        return $user->belongsToTeam($team) && $comunidad->team_id === $team->id;
    }

    public function create(User $user, Team $team): bool
    {
        return $user->belongsToTeam($team);
    }

    public function update(User $user, Comunidad $comunidad, Team $team): bool
    {
        return $this->canManage($user, $comunidad, $team);
    }

    public function delete(User $user, Comunidad $comunidad, Team $team): bool
    {
        return $this->canManage($user, $comunidad, $team);
    }

    private function canManage(User $user, Comunidad $comunidad, Team $team): bool
    {
        return $user->belongsToTeam($team)
            && $comunidad->team_id === $team->id
            && ($comunidad->creador_id === $user->id
                || $comunidad->miembros()
                    ->whereKey($user->id)
                    ->wherePivot('rol', 'admin')
                    ->exists());
    }
}
