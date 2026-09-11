<?php

namespace App\Policies;

use App\Enums\TeamRole;
use App\Models\Etiqueta;
use App\Models\Team;
use App\Models\User;

class EtiquetaPolicy
{
    public function viewAny(User $user, Team $team): bool
    {
        return $user->belongsToTeam($team);
    }

    public function view(User $user, Etiqueta $item, Team $team): bool
    {
        return $user->belongsToTeam($team);
    }

    public function create(User $user, Team $team): bool
    {
        return $user->belongsToTeam($team) && $user->teamRole($team)?->isAtLeast(TeamRole::Admin) === true;
    }

    public function update(User $user, Etiqueta $item, Team $team): bool
    {
        return $this->create($user, $team);
    }

    public function delete(User $user, Etiqueta $item, Team $team): bool
    {
        return $this->create($user, $team);
    }
}
