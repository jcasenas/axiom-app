<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Ebook;
use App\Models\User;
use Cloudinary\Cloudinary;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class ProfileController extends Controller
{
    public function index()
    {
        $totalUsers = User::whereHas('role', fn ($q) => $q->whereIn('role_name', ['Student', 'Faculty', 'Librarian']))
            ->count();

        $totalBooks = Ebook::where('status', 'active')->count();

        return view('admin.profile', compact('totalUsers', 'totalBooks'));
    }

    public function updatePhoto(Request $request)
    {
        $request->validate([
            'photo' => 'required|image|mimes:jpeg,jpg,png,webp|max:2048',
        ]);

        $user = Auth::user();

        $cloudinary = new Cloudinary(
            "cloudinary://" . env('CLOUDINARY_API_KEY') . ":" . env('CLOUDINARY_API_SECRET') . "@" . env('CLOUDINARY_CLOUD_NAME')
        );

        // Delete old photo from Cloudinary if present
        if ($user->profile_photo && str_contains($user->profile_photo, 'cloudinary')) {
            $publicId = pathinfo(basename(parse_url($user->profile_photo, PHP_URL_PATH)), PATHINFO_FILENAME);
            $cloudinary->uploadApi()->destroy('profile-photos/' . $publicId);
        }

        // Upload new photo
        $result = $cloudinary->uploadApi()->upload(
            $request->file('photo')->getRealPath(),
            ['folder' => 'profile-photos']
        );

        $user->update(['profile_photo' => $result['secure_url']]);

        return redirect()->route('admin.profile.index')
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

        return redirect()->route('admin.profile.index')
            ->with('toast', ['message' => 'Password changed successfully.', 'type' => 'success']);
    }
}