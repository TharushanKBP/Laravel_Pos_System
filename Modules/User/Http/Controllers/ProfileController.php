<?php

namespace Modules\User\Http\Controllers;

use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Modules\Upload\Entities\Upload;
use Modules\User\Rules\MatchCurrentPassword;
use App\Models\User;

class ProfileController extends Controller
{
    public function edit()
    {
        return view('user::profile');
    }

    public function update(Request $request)
    {
        $request->validate([
            'name'  => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . auth()->id(),
            'image' => 'nullable|file|mimes:jpg,jpeg,png|max:512' // Add validation for image
        ]);

        $user = auth()->user();
        if ($user) {
            $user->update([
                'name'  => $request->name,
                'email' => $request->email
            ]);

            if ($request->hasFile('image')) {
                // Remove existing image if exists
                if ($user->getFirstMedia('avatars')) {
                    $user->getFirstMedia('avatars')->delete();
                }

                // Add new image
                $user->addMediaFromRequest('image')
                    ->toMediaCollection('avatars');
            }

            toast('Profile Updated!', 'success');
        } else {
            toast('User not authenticated!', 'error');
        }

        return back();
    }

    public function updatePassword(Request $request)
    {
        $request->validate([
            'current_password' => ['required', 'max:255', new MatchCurrentPassword()],
            'password'         => 'required|min:8|max:255|confirmed'
        ]);

        $user = auth()->user();
        if ($user) {
            $user->update([
                'password' => Hash::make($request->password)
            ]);

            toast('Password Updated!', 'success');
        } else {
            toast('User not authenticated!', 'error');
        }

        return back();
    }
}
