<?php

namespace App\Http\Controllers;


use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class UserController extends Controller
{

    public function index()
    {
        $users = User::paginate(10);
        return view("modules.users.index", compact('users'));
    }

    public function edit(User $user)
    {
        return view("modules.users.edit", compact('user'));
    }

    public function profilesUpdate(User $user, Request $request)
    {
        $this->authorize('update', $user);

        $this->validate($request, [
            'profile_image' => 'required|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        // Store the uploaded image
        if (!empty($user->profile_image)) {
            Storage::disk('public')->delete($user->profile_image);
        }

        $path = $request->file('profile_image')->store("profiles/{$user->idUsuario}", 'public');

        $user->profile_image = $path;

        $user->update([
            'profile_image' => $user->profile_image,
        ]);

        session()->flash('message', 'Perfil actualizado correctamente!');

        return redirect()->route('users.edit', ['user' => $user->idUsuario]);
    }
}
