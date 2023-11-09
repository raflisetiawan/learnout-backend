<?php

namespace App\Listeners;

use App\Events\AssignUserRole;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\DB;

class AssignUserRoleListener
{
    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(AssignUserRole $event): void
    {
        $user = $event->user;
        // Cari role dengan 'name' 'user'
        $userRole = DB::table('roles')->where('name', 'user')->first();
        // Jika peran ditemukan, atur 'role_id' pengguna
        if ($userRole) {
            DB::table('users')->where('id', $user->id)->update(['role_id' => $userRole->id]);
        }
    }
}
