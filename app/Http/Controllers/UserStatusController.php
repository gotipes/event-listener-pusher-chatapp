<?php

namespace App\Http\Controllers;

use App\Events\UserOnline;
use App\Models\User;
use Illuminate\Http\Request;

class UserStatusController extends Controller
{
    public function online(User $user)
    {
        $user->status = 'online';
        $user->save();
        
        broadcast(new UserOnline($user));
    }

    public function offline(User $user)
    {
        $user->status = 'offline';
        $user->save();
        
        broadcast(new UserOnline($user));
    }

}
