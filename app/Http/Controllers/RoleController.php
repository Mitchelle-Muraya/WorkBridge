<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RoleController extends Controller
{
    // Show role selection form
    public function selectRole()
    {
        return view('auth.select-role');
    }

    // Save chosen role
    public function saveRole(Request $request)
    {
        $request->validate([
            'role' => 'required|in:client,worker',
        ]);

        $user = Auth::user();
        $user->role = $request->role;
        $user->save(); // ✅ this is correct if "role" column exists in users table

        return redirect()->route('dashboard')->with('success', 'Role selected successfully!');
    }
}
