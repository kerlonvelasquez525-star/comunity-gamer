<?php

namespace App\Policies;

use App\Models\Amistad;
use App\Models\User;

class AmistadPolicy
{
    public function update(User $user, Amistad $item): bool
    {
        return $item->user_id === $user->id || $item->amigo_id === $user->id;
    }

    public function delete(User $user, Amistad $item): bool
    {
        return $this->update($user, $item);
    }
}
