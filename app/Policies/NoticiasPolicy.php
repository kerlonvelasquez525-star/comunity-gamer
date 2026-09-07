<?php

namespace App\Policies;

use App\Enums\TeamRole;
use App\Models\noticias;
use App\Models\Team;
use App\Models\User;

/**
 * Política de acceso para las noticias del equipo.
 *
 * Define quién puede ver, crear, editar y eliminar contenido informativo
 * según la pertenencia al equipo y el tipo de rol que tenga el usuario.
 */
class NoticiasPolicy
{
    /**
     * Verifica si el usuario puede acceder a la colección de noticias del equipo.
     */
    public function viewAny(User $user, Team $team): bool
    {
        return $user->belongsToTeam($team);
    }

    /**
     * Permite ver una noticia si pertenece al equipo o es una noticia pública oficial.
     */
    public function view(User $user, noticias $noticia, Team $team): bool
    {
        return $this->belongsToTeam($user, $team) && ($noticia->team_id === $team->id || $noticia->team_id === null);
    }

    /**
     * Controla quién puede publicar nuevas noticias dentro del equipo.
     */
    public function create(User $user, Team $team): bool
    {
        return $user->belongsToTeam($team);
    }

    /**
     * Solo el autor o un administrador puede editar una noticia del equipo.
     */
    public function update(User $user, noticias $noticia, Team $team): bool
    {
        return $noticia->team_id === $team->id && ($this->canManage($user, $team) || $noticia->user_id === $user->id);
    }

    /**
     * Solo el autor o un administrador puede eliminar una noticia del equipo.
     */
    public function delete(User $user, noticias $noticia, Team $team): bool
    {
        return $noticia->team_id === $team->id && ($this->canManage($user, $team) || $noticia->user_id === $user->id);
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
