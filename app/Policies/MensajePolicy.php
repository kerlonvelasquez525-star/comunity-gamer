<?php

namespace App\Policies;

use App\Enums\TeamRole;
use App\Models\Mensaje;
use App\Models\Team;
use App\Models\User;

class MensajePolicy
{
    public function viewAny(User $user, Team $team): bool
    {
        return $user->belongsToTeam($team);
    }

    public function view(User $user, Mensaje $item, Team $team): bool
    {
        return $user->belongsToTeam($team) && $item->canal?->comunidad?->team_id === $team->id;
    }

    public function create(User $user, Team $team): bool
    {
        return $user->belongsToTeam($team);
    }

    public function delete(User $user, Mensaje $item, Team $team): bool
    {
        return $user->belongsToTeam($team)
            && $item->canal?->comunidad?->team_id === $team->id
            && (($user->teamRole($team)?->isAtLeast(TeamRole::Admin) === true) || $item->user_id === $user->id);
    }
}
