<?php

namespace App\Http\Controllers\Admin;

use App\Notifications\AxiomNotification;
use Illuminate\Support\Facades\Notification;
use App\Http\Controllers\Controller;
use App\Models\Department;
use Illuminate\Http\Request;

class DepartmentController extends Controller
{
    public function index()
    {
        $departments = Department::withCount('users')
            ->orderBy('department_name')
            ->paginate(10);

        return view('admin.departments.index', compact('departments'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'department_name' => 'required|string|max:50|unique:departments,department_name',
            'description'     => 'nullable|string|max:150',
        ]);

        Department::create([
            'department_name' => $request->department_name,
            'description'     => $request->description,
        ]);

        return redirect()->route('admin.departments.index')
        ->with('toast', ['message' => 'Department added successfully.', 'type' => 'success']);
    }

    public function update(Request $request, Department $department)
    {
        $request->validate([
            'department_name' => 'required|string|max:50|unique:departments,department_name,' . $department->department_id . ',department_id',
            'description'     => 'nullable|string|max:150',
        ]);

        $department->update([
            'department_name' => $request->department_name,
            'description'     => $request->description,
        ]);

        return redirect()->route('admin.departments.index')
        ->with('toast', ['message' => 'Department updated successfully.', 'type' => 'success']);
    }

    public function destroy(Department $department)
    {
        // Guard: prevent deletion if users are still assigned
        if ($department->users()->count() > 0) {
            return redirect()->route('admin.departments.index')
    ->with('toast', ['message' => 'Cannot delete: Department has existing users.', 'type' => 'error']);
        }

        $department->delete();

        return redirect()->route('admin.departments.index')
        ->with('toast', ['message' => 'Department deleted successfully.', 'type' => 'success']);
    }
}