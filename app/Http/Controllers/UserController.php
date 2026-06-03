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
        $this->authorize('create', User::class);

        $request->validate([
            'first_name' => ['required', 'string', 'max:255'],
            'last_name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:'.User::class],
            'password' => ['required', Password::defaults()],
            'timezone' => ['required', 'string', 'max:255'],
        ]);

        $user = User::create([
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'timezone' => $request->timezone,
        ]);

        $user->is_admin = $request->boolean('is_admin');
        $user->save();

        return redirect()->back()->with('status', 'user-created');
    }

    /**
     * Remove the specified user from storage.
     */
    public function destroy(User $user)
    {
        $this->authorize('delete', $user);

        if ($user->is_admin && ! User::where('is_admin', true)->where('id', '!=', $user->id)->exists()) {
            return redirect()->back()->withErrors(['delete_user' => 'You cannot delete the last administrator account.']);
        }

        $user->delete();

        return redirect()->back()->with('status', 'user-deleted');
    }
}
