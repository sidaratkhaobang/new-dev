<?php

namespace App\Broadcasting;

use App\Models\User;

class NotificationUserChannel
{
    /**
     * Create a new channel instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Authenticate the user's access to the channel.
     *
     * @param  \App\Models\User  $user
     * @return array|bool
     */
    public function join(User $user, $id)
    {
        return (strcmp($user->id, $id) == 0);
    }
}
