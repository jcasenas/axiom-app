<?php

namespace App\Http\Controllers\Librarian;

use App\Notifications\AxiomNotification;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class ProfileController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        $totalApproved = $user->borrowings()
            ->whereNotNull('approved_by')
            ->where('approved_by', $user->user_id)
            ->count();

        return view('librarian.profile', compact('totalApproved'));
    }

    public function updatePhoto(Request $request)
    {
        $request->validate([
            'photo' => 'required|image|mimes:jpeg,jpg,png,webp|max:2048',
        ]);

        $user = Auth::user();

        if ($user->profile_photo) {
            Storage::disk('public')->delete($user->profile_photo);
        }

        $path = $request->file('photo')->store('profile-photos', 'public');
        $user->update(['profile_photo' => $path]);

        return redirect()->route('librarian.profile')
        ->with('toast', ['message' => 'Profile photo updated.', 'type' => 'success']);
    }

    public function updatePassword(Request $request)
    {
        $request->validate([
            'current_password' => 'required|string',
            'password'         => 'required|string|min:8|confirmed',
        ]);

        $user = Auth::user();

        if (! Hash::check($request->current_password, $user->password)) {
            return back()->withErrors(['current_password' => 'Current password is incorrect.']);
        }

        $user->update(['password' => Hash::make($request->password)]);

        return redirect()->route('librarian.profile')
        ->with('toast', ['message' => 'Password changed successfully.', 'type' => 'success']);
    }
}