<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Spatie\Permission\Models\Role;

class AdminUserController extends Controller
{
    public function index()
    {
        $users = User::with('roles')->paginate(15);
        return view('admin.users.index', compact('users'));
    }

    public function create()
    {
        $roles = Role::all();
        return view('admin.users.create', compact('roles'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string|max:500',
            'role' => 'required|exists:roles,name',
            'is_active' => 'boolean',
            'email_verified' => 'boolean',
        ]);

        $userData = [
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'phone' => $request->phone,
            'address' => $request->address,
            'is_active' => $request->has('is_active'),
        ];

        // Handle email verification
        if ($request->has('email_verified')) {
            $userData['email_verified_at'] = now();
        }

        // Debug logging - remove this after testing
        Log::info('Admin user create debug', [
            'email_verified_checkbox' => $request->has('email_verified'),
            'email_verified_value' => $request->input('email_verified'),
            'email_verified_at_setting' => $userData['email_verified_at'] ?? null,
        ]);

        $user = User::create($userData);

        $user->assignRole($request->role);

        return redirect()->route('admin.users.index')->with('success', 'User created successfully.');
    }

    public function show(User $user)
    {
        $user->load(['roles', 'orders.orderItems.product']);
        return view('admin.users.show', compact('user'));
    }

    public function edit(User $user)
    {
        $roles = Role::all();
        return view('admin.users.edit', compact('user', 'roles'));
    }

    public function update(Request $request, User $user)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $user->id,
            'password' => 'nullable|string|min:8|confirmed',
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string|max:500',
            'role' => 'required|exists:roles,name',
            'is_active' => 'boolean',
            'email_verified' => 'boolean',
        ]);

        // Update basic fields
        $user->update([
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
            'address' => $request->address,
            'is_active' => $request->has('is_active'),
        ]);

        // Handle password update
        if ($request->filled('password')) {
            $user->password = Hash::make($request->password);
            $user->save();
        }

        // Handle email verification - direct assignment to avoid any interference
        if ($request->has('email_verified')) {
            // Checkbox is checked - set email as verified
            $user->email_verified_at = now();
        } else {
            // Checkbox is unchecked - set email as unverified
            $user->email_verified_at = null;
        }
        $user->save();

        $user->syncRoles([$request->role]);

        // Final verification check
        $user->refresh();
        Log::info('Admin user update result', [
            'user_id' => $user->id,
            'email_verified_at_after_update' => $user->email_verified_at,
            'is_verified' => $user->hasVerifiedEmail(),
        ]);

        return redirect()->route('admin.users.index')->with('success', 'User updated successfully.');
    }

    public function destroy(User $user)
    {
        if ($user->hasRole('admin') && User::role('admin')->count() <= 1) {
            return redirect()->route('admin.users.index')->with('error', 'Cannot delete the last admin user.');
        }

        $user->delete();
        return redirect()->route('admin.users.index')->with('success', 'User deleted successfully.');
    }

    public function activate(User $user)
    {
        $user->update(['is_active' => true]);
        return redirect()->route('admin.users.show', $user)->with('success', 'User activated successfully.');
    }

    public function deactivate(User $user)
    {
        if ($user->hasRole('admin') && User::role('admin')->where('is_active', true)->count() <= 1) {
            return redirect()->route('admin.users.show', $user)->with('error', 'Cannot deactivate the last active admin user.');
        }

        $user->update(['is_active' => false]);
        return redirect()->route('admin.users.show', $user)->with('success', 'User deactivated successfully.');
    }

    public function verifyEmail(User $user)
    {
        $user->update(['email_verified_at' => now()]);
        return redirect()->route('admin.users.show', $user)->with('success', 'User email verified successfully.');
    }
}
