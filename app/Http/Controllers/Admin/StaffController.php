<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Admin;
use App\Models\ActivityLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class StaffController extends Controller
{
    public function index()
    {
        $staff = Admin::orderBy('id')->get();
        return view('admin.staff.index', compact('staff'));
    }

    public function create()
    {
        return view('admin.staff.form');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:admins',
            'role' => [
                'required',
                Rule::in([
                    Admin::ROLE_SUPER_ADMIN,
                    Admin::ROLE_ADMIN,
                    Admin::ROLE_RECEPTIONIST,
                    Admin::ROLE_REVENUE,
                    Admin::ROLE_HOUSEKEEPING
                ])
            ],
            'password' => 'required|string|min:8|confirmed',
        ]);

        $staff = Admin::create([
            'hotel_id' => auth('admin')->user()->hotel_id,
            'name' => $request->name,
            'email' => $request->email,
            'role' => $request->role,
            'password' => Hash::make($request->password),
        ]);

        ActivityLog::log('Staff Created', "Created new staff member: {$staff->name} ({$staff->role})", $staff);

        return redirect()->route('admin.staff.index')->with('success', 'Staff member created successfully.');
    }

    public function edit(Admin $staff)
    {
        // Renaming the variable for the view to match the parameter name 'staff'
        return view('admin.staff.form', compact('staff'));
    }

    public function update(Request $request, Admin $staff)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('admins')->ignore($staff->id)],
            'role' => [
                'required',
                Rule::in([
                    Admin::ROLE_SUPER_ADMIN,
                    Admin::ROLE_ADMIN,
                    Admin::ROLE_RECEPTIONIST,
                    Admin::ROLE_REVENUE,
                    Admin::ROLE_HOUSEKEEPING
                ])
            ],
            'password' => 'nullable|string|min:8|confirmed',
        ]);

        $data = [
            'name' => $request->name,
            'email' => $request->email,
            'role' => $request->role,
        ];

        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
        }

        $staff->update($data);

        ActivityLog::log('Staff Updated', "Updated staff member: {$staff->name}", $staff);

        return redirect()->route('admin.staff.index')->with('success', 'Staff member updated successfully.');
    }

    public function destroy(Admin $staff)
    {
        if ($staff->id === auth('admin')->id()) {
            return back()->with('error', 'You cannot delete your own account.');
        }

        if ($staff->role === Admin::ROLE_SUPER_ADMIN && Admin::where('role', Admin::ROLE_SUPER_ADMIN)->count() <= 1) {
            return back()->with('error', 'Cannot delete the last Super Admin.');
        }

        $name = $staff->name;
        $staff->delete();

        ActivityLog::log('Staff Deleted', "Deleted staff member: {$name}");

        return redirect()->route('admin.staff.index')->with('success', 'Staff member deleted successfully.');
    }
}
