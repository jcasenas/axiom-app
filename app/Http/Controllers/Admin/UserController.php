<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Department;
use App\Models\User;
use App\Models\UserRole;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    // ── Index ──────────────────────────────────────────────
    public function index(Request $request)
    {
        $query = User::with(['role', 'department'])
                     ->whereHas('role', fn($q) =>
                         $q->whereNotIn('role_name', ['Admin'])
                     );

        if ($request->filled('department') && $request->department !== 'all') {
            $query->where('department_id', $request->department);
        }

        if ($request->filled('role') && $request->role !== 'all') {
            $query->where('role_id', $request->role);
        }

        if ($request->filled('status') && $request->status !== 'all') {
            $query->where('account_status', $request->status);
        }

        $users       = $query->orderBy('full_name')->paginate(10)->withQueryString();
        $departments = Department::orderBy('department_name')->get();
        $roles       = UserRole::whereNotIn('role_name', ['Admin'])->get();

        return view('admin.users.index', compact('users', 'departments', 'roles'));
    }

    // ── Create (unused — modal handles this inline) ────────
    public function create()
    {
        // Redirect back to index; the Add User modal is inline
        return redirect()->route('admin.users.index');
    }

    // ── Store ──────────────────────────────────────────────
    public function store(Request $request)
    {
        $request->validate([
            'full_name'      => 'required|string|max:255',
            'email'          => 'required|email|unique:users,email',
            'role_id'        => 'required|exists:user_roles,role_id',
            'department_id'  => 'nullable|exists:departments,department_id',
            'password'       => 'required|string|min:8|confirmed',
            'account_status' => 'required|in:pending,active,inactive',
        ]);

        User::create([
            'full_name'      => $request->full_name,
            'email'          => $request->email,
            'role_id'        => $request->role_id,
            'department_id'  => $request->department_id ?: null,
            'password'       => Hash::make($request->password),
            'account_status' => $request->account_status,
        ]);

        return redirect()->route('admin.users.index')
                         ->with('success', 'User created successfully.');
    }

    // ── Edit ───────────────────────────────────────────────
    public function edit(User $user)
    {
        $departments = Department::orderBy('department_name')->get();
        $roles       = UserRole::whereNotIn('role_name', ['Admin'])->get();

        return view('admin.users.edit', compact('user', 'departments', 'roles'));
    }

    // ── Update ─────────────────────────────────────────────
    public function update(Request $request, User $user)
    {
        $request->validate([
            'full_name'     => 'required|string|max:255',
            'email'         => ['required', 'email', Rule::unique('users', 'email')->ignore($user->user_id, 'user_id')],
            'role_id'       => 'required|exists:user_roles,role_id',
            'department_id' => 'nullable|exists:departments,department_id',
            'password'      => 'nullable|string|min:8|confirmed',
        ]);

        $data = [
            'full_name'     => $request->full_name,
            'email'         => $request->email,
            'role_id'       => $request->role_id,
            'department_id' => $request->department_id ?: null,
        ];

        // Only update password if a new one was provided
        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
        }

        $user->update($data);

        return redirect()->route('admin.users.index')
                         ->with('success', 'User updated successfully.');
    }

    // ── Approve ────────────────────────────────────────────
    public function approve(User $user)
    {
        $user->update(['account_status' => 'active']);         $user->notify(new \App\Notifications\AccountApproved());
        return back()->with('success', 'Account approved successfully.');
    }

    // ── Reject ─────────────────────────────────────────────
    public function reject(User $user)
    {
        $user->update(['account_status' => 'inactive']);         $user->notify(new \App\Notifications\AccountRejected());
        return back()->with('success', 'Account rejected.');
    }

    // ── Deactivate ─────────────────────────────────────────
    public function deactivate(User $user)
    {
        $user->update(['account_status' => 'inactive']);         $user->notify(new \App\Notifications\AccountDeactivated());
        return back()->with('success', 'Account deactivated.');
    }
}
