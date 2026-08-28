<?php

namespace App\Policies;

use App\Enums\TeamRole;
use App\Models\Comentarios;
use App\Models\Team;
use App\Models\User;

class ComentariosPolicy
{
    public function viewAny(User $user, Team $team): bool
    {
        return $user->belongsToTeam($team);
    }

    public function view(User $user, Comentarios $comentario, Team $team): bool
    {
        return $user->belongsToTeam($team) && $comentario->team_id === $team->id;
    }

    public function create(User $user, Team $team): bool
    {
        return $user->belongsToTeam($team);
    }

    public function update(User $user, Comentarios $comentario, Team $team): bool
    {
        return $this->canManage($user, $team) || ($comentario->team_id === $team->id && $comentario->id_usuario === $user->id);
    }

    public function delete(User $user, Comentarios $comentario, Team $team): bool
    {
        return $this->canManage($user, $team) || ($comentario->team_id === $team->id && $comentario->id_usuario === $user->id);
    }

    private function canManage(User $user, Team $team): bool
    {
        return $user->belongsToTeam($team)
            && $user->teamRole($team)?->isAtLeast(TeamRole::Admin) === true;
    }
}
