<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Borrowing;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class ProfileController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        $totalBorrowed = Borrowing::where('user_id', $user->user_id)->count();
        $currentlyBorrowed = Borrowing::where('user_id', $user->user_id)
            ->whereIn('status', ['pending', 'active', 'due_soon'])
            ->count();

        return view('student.profile', compact('totalBorrowed', 'currentlyBorrowed'));
    }

    public function updatePhoto(Request $request)
    {
        $request->validate([
            'photo' => 'required|image|mimes:jpeg,jpg,png,webp|max:2048',
        ]);

        $user = Auth::user();

        if ($user->profile_photo && str_contains($user->profile_photo, 'cloudinary')) {
            $publicId = pathinfo(basename(parse_url($user->profile_photo, PHP_URL_PATH)), PATHINFO_FILENAME);
            cloudinary()->destroy('profile-photos/' . $publicId);
        }

        $result = cloudinary()->upload($request->file('photo')->getRealPath(), [
            'folder' => 'profile-photos',
        ]);

        $user->update(['profile_photo' => $result->getSecurePath()]);

        $rp = $user->isFaculty() ? 'faculty' : 'student';

        return redirect()->route("{$rp}.profile")
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

        $rp = $user->isFaculty() ? 'faculty' : 'student';

        return redirect()->route("{$rp}.profile")
            ->with('toast', ['message' => 'Password changed successfully.', 'type' => 'success']);
    }
}