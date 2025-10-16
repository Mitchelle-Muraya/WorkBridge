<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{
    public function switchMode()
    {
        $user = Auth::user();

        // Toggle mode between worker and client
        $user->mode = $user->mode === 'worker' ? 'client' : 'worker';
        $user->save();

        // Redirect appropriately
        if ($user->mode === 'worker') {
            return redirect()->route('worker.dashboard')->with('success', 'Switched to Worker Mode');
        } else {
            return redirect()->route('client.dashboard')->with('success', 'Switched to Client Mode');
        }
    }
}
