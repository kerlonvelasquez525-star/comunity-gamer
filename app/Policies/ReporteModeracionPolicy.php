<?php

namespace App\Policies;

use App\Enums\TeamRole;
use App\Models\ReporteModeracion;
use App\Models\Team;
use App\Models\User;

class ReporteModeracionPolicy
{
    public function viewAny(User $user, Team $team): bool
    {
        return $this->admin($user, $team);
    }

    public function view(User $user, ReporteModeracion $item, Team $team): bool
    {
        return $this->admin($user, $team) && $item->team_id === $team->id;
    }

    public function create(User $user, Team $team): bool
    {
        return $user->belongsToTeam($team);
    }

    public function update(User $user, ReporteModeracion $item, Team $team): bool
    {
        return $this->admin($user, $team) && $item->team_id === $team->id;
    }

    private function admin(User $user, Team $team): bool
    {
        return $user->belongsToTeam($team) && $user->teamRole($team)?->isAtLeast(TeamRole::Admin) === true;
    }
}
