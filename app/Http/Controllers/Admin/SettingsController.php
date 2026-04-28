<?php

namespace App\Http\Controllers\Admin;

use App\Notifications\AxiomNotification;
use Illuminate\Support\Facades\Notification;
use App\Http\Controllers\Controller;
use App\Models\SystemSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SettingsController extends Controller
{
    /** Keys this controller manages — whitelist to prevent mass-assignment abuse. */
    private const MANAGED_KEYS = [
        'library_name',
        'borrow_window_days',
        'max_borrows_student',
        'max_borrows_faculty',
        'system_status',
    ];

    public function index()
    {
        // Load all settings into a simple key => value map for the blade
        $settings = SystemSetting::whereIn('setting_key', self::MANAGED_KEYS)
            ->pluck('setting_value', 'setting_key')
            ->toArray();

        return view('admin.settings', compact('settings'));
    }

    public function update(Request $request)
    {
        $request->validate([
            'library_name'        => 'required|string|max:100',
            'borrow_window_days'  => 'required|integer|min:1|max:365',
            'max_borrows_student' => 'required|integer|min:1|max:20',
            'max_borrows_faculty' => 'required|integer|min:1|max:20',
            'system_status'       => 'required|in:active,maintenance',
        ]);

        $adminId = Auth::id();

        foreach (self::MANAGED_KEYS as $key) {
            SystemSetting::where('setting_key', $key)->update([
                'setting_value' => $request->input($key),
                'updated_by'    => $adminId,
            ]);
        }

        return redirect()->route('admin.settings.index')
        ->with('toast', ['message' => 'Settings saved successfully.', 'type' => 'success']);
    }
}