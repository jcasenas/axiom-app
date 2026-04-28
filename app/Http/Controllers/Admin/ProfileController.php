<?php

namespace App\Http\Controllers\Admin;

use App\Notifications\AxiomNotification;
use Illuminate\Support\Facades\Notification;
use App\Http\Controllers\Controller;
use App\Models\Ebook;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class ProfileController extends Controller
{
    public function index()
    {
        $totalUsers = User::whereHas('role', fn ($q) => $q->whereIn('role_name', ['Student', 'Faculty', 'Librarian']))
            ->count();

        $totalBooks = Ebook::where('status', 'active')->count();

        return view('admin.profile', compact('totalUsers', 'totalBooks'));
    }

    /**
     * Update profile photo.
     * File is stored in storage/app/public/profile-photos/ and
     * referenced via asset('storage/...') in the blade.
     */
    public function updatePhoto(Request $request)
    {
        $request->validate([
            'photo' => 'required|image|mimes:jpeg,jpg,png,webp|max:2048',
        ]);

        $user = Auth::user();

        // Delete old photo if present
        if ($user->profile_photo) {
            Storage::disk('public')->delete($user->profile_photo);
        }

        $path = $request->file('photo')->store('profile-photos', 'public');

        $user->update(['profile_photo' => $path]);

        return redirect()->route('admin.profile.index')
        ->with('toast', ['message' => 'Profile photo updated.', 'type' => 'success']);
    }

    /**
     * Change password.
     */
    public function updatePassword(Request $request)
    {
        $request->validate([
            'current_password'      => 'required|string',
            'password'              => 'required|string|min:8|confirmed',
        ]);

        $user = Auth::user();

        if (! Hash::check($request->current_password, $user->password)) {
            return back()->withErrors(['current_password' => 'Current password is incorrect.']);
        }

        $user->update([
            'password' => Hash::make($request->password),
        ]);

        return redirect()->route('admin.profile.index')
        ->with('toast', ['message' => 'Password changed successfully.', 'type' => 'success']);
    }
}