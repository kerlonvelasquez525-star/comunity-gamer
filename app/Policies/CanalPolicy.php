<?php

namespace App\Policies;

use App\Enums\TeamRole;
use App\Models\Canal;
use App\Models\Team;
use App\Models\User;

class CanalPolicy
{
    public function viewAny(User $user, Team $team): bool
    {
        return $user->belongsToTeam($team);
    }

    public function view(User $user, Canal $item, Team $team): bool
    {
        return $user->belongsToTeam($team) && $item->comunidad?->team_id === $team->id;
    }

    public function create(User $user, Team $team): bool
    {
        return $user->belongsToTeam($team);
    }

    public function update(User $user, Canal $item, Team $team): bool
    {
        return $user->belongsToTeam($team)
            && $item->comunidad->team_id === $team->id
            && (($user->teamRole($team)?->isAtLeast(TeamRole::Admin) === true) || $item->comunidad->creador_id === $user->id);
    }

    public function delete(User $user, Canal $item, Team $team): bool
    {
        return $this->update($user, $item, $team);
    }
}
