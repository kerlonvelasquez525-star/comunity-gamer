<?php

namespace App\Policies;

use App\Enums\TeamRole;
use App\Models\Aporte;
use App\Models\Team;
use App\Models\User;

class AportePolicy
{
    public function viewAny(User $user, Team $team): bool
    {
        return $user->belongsToTeam($team);
    }

    public function view(User $user, Aporte $item, Team $team): bool
    {
        return $user->belongsToTeam($team) && $item->hilo?->foro?->comunidad?->team_id === $team->id;
    }

    public function create(User $user, Team $team): bool
    {
        return $user->belongsToTeam($team);
    }

    public function update(User $user, Aporte $item, Team $team): bool
    {
        return $user->belongsToTeam($team) && (($user->teamRole($team)?->isAtLeast(TeamRole::Admin) === true) || $item->id_usuario === $user->id);
    }

    public function delete(User $user, Aporte $item, Team $team): bool
    {
        return $this->update($user, $item, $team);
    }
}
