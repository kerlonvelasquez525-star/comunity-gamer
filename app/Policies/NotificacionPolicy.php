<?php

namespace App\Policies;

use App\Models\Notificacion;
use App\Models\User;

class NotificacionPolicy
{
    public function view(User $user, Notificacion $item): bool
    {
        return $item->user_id === $user->id;
    }

    public function update(User $user, Notificacion $item): bool
    {
        return $this->view($user, $item);
    }

    public function delete(User $user, Notificacion $item): bool
    {
        return $this->view($user, $item);
    }
}
