<?php

namespace App\Policies;

use App\Enums\TeamRole;
use App\Models\Idioma;
use App\Models\Team;
use App\Models\User;

class IdiomaPolicy
{
    public function viewAny(User $user, Team $team): bool
    {
        return $user->belongsToTeam($team);
    }

    public function view(User $user, Idioma $item, Team $team): bool
    {
        return $user->belongsToTeam($team);
    }

    public function create(User $user, Team $team): bool
    {
        return $user->belongsToTeam($team) && $user->teamRole($team)?->isAtLeast(TeamRole::Admin) === true;
    }

    public function update(User $user, Idioma $item, Team $team): bool
    {
        return $this->create($user, $team);
    }

    public function delete(User $user, Idioma $item, Team $team): bool
    {
        return $this->create($user, $team);
    }
}
