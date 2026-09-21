<?php

namespace App\Policies;

use App\Enums\TeamRole;
use App\Models\ReporteSoporte;
use App\Models\Team;
use App\Models\User;

class ReporteSoportePolicy
{
    public function viewAny(User $user, Team $team): bool
    {
        return $user->belongsToTeam($team);
    }

    public function view(User $user, ReporteSoporte $item, Team $team): bool
    {
        return $user->belongsToTeam($team)
            && $item->team_id === $team->id
            && ($item->id_usuario === $user->id || $user->teamRole($team)?->isAtLeast(TeamRole::Admin) === true);
    }

    public function create(User $user, Team $team): bool
    {
        return $user->belongsToTeam($team);
    }

    public function update(User $user, ReporteSoporte $item, Team $team): bool
    {
        return $this->view($user, $item, $team);
    }

    public function delete(User $user, ReporteSoporte $item, Team $team): bool
    {
        return $this->view($user, $item, $team);
    }

    public function respond(User $user, ReporteSoporte $item, Team $team): bool
    {
        return $user->belongsToTeam($team)
            && $item->team_id === $team->id
            && $user->teamRole($team)?->isAtLeast(TeamRole::Admin) === true;
    }

    public function vote(User $user, ReporteSoporte $item, Team $team): bool
    {
        return $this->viewAny($user, $team) && $item->team_id === $team->id;
    }
}
