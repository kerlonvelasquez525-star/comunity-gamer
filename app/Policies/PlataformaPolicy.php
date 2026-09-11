<?php

namespace App\Policies;

use App\Enums\TeamRole;
use App\Models\Plataforma;
use App\Models\Team;
use App\Models\User;

class PlataformaPolicy
{
    public function viewAny(User $user, Team $team): bool
    {
        return $user->belongsToTeam($team);
    }

    public function view(User $user, Plataforma $item, Team $team): bool
    {
        return $user->belongsToTeam($team);
    }

    public function create(User $user, Team $team): bool
    {
        return $user->belongsToTeam($team) && $user->teamRole($team)?->isAtLeast(TeamRole::Admin) === true;
    }

    public function update(User $user, Plataforma $item, Team $team): bool
    {
        return $this->create($user, $team);
    }

    public function delete(User $user, Plataforma $item, Team $team): bool
    {
        return $this->create($user, $team);
    }
}
