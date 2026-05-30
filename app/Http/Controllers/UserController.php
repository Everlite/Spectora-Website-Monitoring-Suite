<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class UserController extends Controller
{
    /**
     * Store a newly created user in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'first_name' => ['required', 'string', 'max:255'],
            'last_name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:'.User::class],
            'password' => ['required', Password::defaults()],
            'timezone' => ['required', 'string', 'max:255'],
        ]);

        User::create([
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'is_admin' => $request->has('is_admin'),
            'timezone' => $request->timezone,
        ]);

        return redirect()->back()->with('status', 'user-created');
    }

    /**
     * Remove the specified user from storage.
     */
    public function destroy(User $user)
    {
        if ($user->id === auth()->id()) {
            return redirect()->back()->withErrors(['delete_user' => 'You cannot delete your own account.']);
        }

        if ($user->is_admin && ! User::where('is_admin', true)->where('id', '!=', $user->id)->exists()) {
            return redirect()->back()->withErrors(['delete_user' => 'You cannot delete the last administrator account.']);
        }

        $user->delete();

        return redirect()->back()->with('status', 'user-deleted');
    }
}
