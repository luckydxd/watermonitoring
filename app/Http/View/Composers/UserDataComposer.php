<?php

namespace App\Http\View\Composers;

use Illuminate\View\View;
use Illuminate\Support\Facades\Auth;

class UserDataComposer
{
    public function compose(View $view)
    {
        $user = Auth::user();

        $view->with([
            'currentUserName' => ($user?->userData)->name ?? 'Guest',
            'currentUserRole' => $user?->roles->first()?->name ?? 'Guest',
            'currentUserAvatar' => asset('demo2/assets/img/avatars/' . ($user ? '1.png' : 'default.png'))
        ]);
    }
}
