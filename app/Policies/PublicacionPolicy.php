<?php

namespace App\Policies;

use App\Enums\TeamRole;
use App\Models\Publicacion;
use App\Models\Team;
use App\Models\User;

class PublicacionPolicy
{
    public function viewAny(User $user, Team $team): bool
    {
        return $user->belongsToTeam($team);
    }

    public function view(User $user, Publicacion $item, Team $team): bool
    {
        return $user->belongsToTeam($team) && $item->team_id === $team->id;
    }

    public function create(User $user, Team $team): bool
    {
        return $user->belongsToTeam($team);
    }

    public function update(User $user, Publicacion $item, Team $team): bool
    {
        return $this->manage($user, $team) || ($item->team_id === $team->id && $item->user_id === $user->id);
    }

    public function delete(User $user, Publicacion $item, Team $team): bool
    {
        return $this->update($user, $item, $team);
    }

    private function manage(User $user, Team $team): bool
    {
        return $user->belongsToTeam($team) && $user->teamRole($team)?->isAtLeast(TeamRole::Admin) === true;
    }
}
