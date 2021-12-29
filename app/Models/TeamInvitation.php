<?php

namespace App\Models;

use Laravel\Jetstream\Jetstream;
use Laravel\Jetstream\TeamInvitation as JetstreamTeamInvitation;

class TeamInvitation extends JetstreamTeamInvitation
{
    /**
     * {@inheritdoc}
     */
    protected $fillable = [
        'email',
        'role',
    ];

    /**
     * {@inheritdoc}
     */
    public function team()
    {
        return $this->belongsTo(Jetstream::teamModel());
    }
}
