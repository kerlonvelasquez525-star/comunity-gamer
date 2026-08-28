<?php

namespace App\Http\Controllers;

use App\Models\Team;
use Illuminate\Http\Request;

abstract class Controller
{
    protected function currentTeam(Request $request): Team
    {
        $team = $request->route('current_team');

        if ($team instanceof Team) {
            return $team;
        }

        return Team::where('slug', $team)->firstOrFail();
    }
}
