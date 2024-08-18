<?php

namespace App\Actions\Jetstream;

use JohannDesarrollador\Notifications\Contracts\DeletesTeams;

class DeleteTeam implements DeletesTeams
{
    /**
     * Delete the given team.
     *
     * @param  mixed  $team
     * @return void
     */
    public function delete($team)
    {
        $team->purge();
    }
}
