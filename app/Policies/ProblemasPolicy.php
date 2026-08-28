<?php

namespace App\Policies;

use App\Enums\TeamRole;
use App\Models\Problemas;
use App\Models\Team;
use App\Models\User;

class ProblemasPolicy
{
    public function viewAny(User $user, Team $team): bool
    {
        return $user->belongsToTeam($team);
    }

    public function view(User $user, Problemas $problema, Team $team): bool
    {
        return $user->belongsToTeam($team) && $problema->team_id === $team->id;
    }

    public function create(User $user, Team $team): bool
    {
        return $user->belongsToTeam($team);
    }

    public function update(User $user, Problemas $problema, Team $team): bool
    {
        return $this->canManage($user, $team) || ($problema->team_id === $team->id && $problema->user_id === $user->id);
    }

    public function delete(User $user, Problemas $problema, Team $team): bool
    {
        return $this->canManage($user, $team) || ($problema->team_id === $team->id && $problema->user_id === $user->id);
    }

    private function canManage(User $user, Team $team): bool
    {
        return $user->belongsToTeam($team)
            && $user->teamRole($team)?->isAtLeast(TeamRole::Admin) === true;
    }
}
