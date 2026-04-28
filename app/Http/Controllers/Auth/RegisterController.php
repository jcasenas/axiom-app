<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Department;
use App\Models\User;
use App\Models\UserRole;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class RegisterController extends Controller
{
    public function showRegister()
    {
        $departments = Department::all();
        $roles       = UserRole::whereIn('role_name', ['Student', 'Faculty'])->get();
        return view('auth.register', compact('departments', 'roles'));
    }

    public function register(Request $request)
    {
        $request->validate([
            'first_name' => 'required|string|max:50',
            'last_name'  => 'required|string|max:50',
            'email'      => 'required|email|unique:users,email',
            'department' => 'required|exists:departments,department_id',
            'role'       => 'required|exists:user_roles,role_id',
            'password'   => 'required|min:8|confirmed',
        ]);

        User::create([
            'full_name'      => $request->first_name . ' ' . $request->last_name,
            'email'          => $request->email,
            'password'       => Hash::make($request->password),
            'department_id'  => $request->department,
            'role_id'        => $request->role,
            'account_status' => 'pending',
        ]);

        return redirect()->route('login')
            ->with('success', 'Registration submitted! Your account is pending approval from the Administrator.');
    }
}