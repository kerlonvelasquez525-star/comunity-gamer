<?php

namespace App\Policies;

use App\Enums\TeamRole;
use App\Models\noticias;
use App\Models\Team;
use App\Models\User;

class NoticiasPolicy
{
    public function viewAny(User $user, Team $team): bool
    {
        return $user->belongsToTeam($team);
    }

    public function view(User $user, noticias $noticia, Team $team): bool
    {
        return $this->belongsToTeam($user, $team) && $noticia->team_id === $team->id;
    }

    public function create(User $user, Team $team): bool
    {
        return $user->belongsToTeam($team);
    }

    public function update(User $user, noticias $noticia, Team $team): bool
    {
        return $this->canManage($user, $team) || ($noticia->team_id === $team->id && $noticia->user_id === $user->id);
    }

    public function delete(User $user, noticias $noticia, Team $team): bool
    {
        return $this->canManage($user, $team) || ($noticia->team_id === $team->id && $noticia->user_id === $user->id);
    }

    private function belongsToTeam(User $user, Team $team): bool
    {
        return $user->belongsToTeam($team);
    }

    private function canManage(User $user, Team $team): bool
    {
        return $this->belongsToTeam($user, $team)
            && $user->teamRole($team)?->isAtLeast(TeamRole::Admin) === true;
    }
}
