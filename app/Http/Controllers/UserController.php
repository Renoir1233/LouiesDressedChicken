<?php
// app/Http/Controllers/UserController.php - Complete fixed version

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Role;
use App\Models\AuditLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('permission:users.*')->only(['index', 'create', 'store', 'edit', 'update', 'destroy']);
    }

    public function index(Request $request)
    {
        $search = $request->get('search');
        $role = $request->get('role');
        
        $users = User::when($search, function($query) use ($search) {
            return $query->where('name', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%");
        })
        ->when($role, function($query) use ($role) {
            return $query->where('role', $role);
        })
        ->withTrashed()
        ->latest()
        ->paginate(15);

        $roles = Role::all();
        
        return view('users.index', compact('users', 'roles', 'search', 'role'));
    }

    public function create()
    {
        $roles = Role::all();
        return view('users.create', compact('roles'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:8|confirmed',
            'role' => 'required|string|exists:roles,slug',
            'avatar' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'is_active' => 'sometimes|boolean'
        ]);

        $data = $request->except('password', 'avatar');
        $data['password'] = Hash::make($request->password);
        $data['is_active'] = (int) $request->input('is_active', 1);

        if ($request->hasFile('avatar')) {
            $data['avatar'] = $request->file('avatar')->store('avatars', 'public');
        }

        $user = User::create($data);

        // Log user creation
        AuditLog::create([
            'action' => 'created',
            'model_type' => User::class,
            'model_id' => $user->id,
            'new_values' => $user->toArray(),
            'description' => "User {$user->name} was created",
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'url' => $request->url(),
            'method' => $request->method(),
            'user_id' => auth()->id(),
        ]);

        return redirect()->route('users.index')
            ->with('success', 'User created successfully.');
    }

    public function show(User $user)
    {
        $user->load(['auditLogs' => function($query) {
            $query->latest()->limit(10);
        }]);
        
        return view('users.show', compact('user'));
    }

    public function edit(User $user)
    {
        $roles = Role::all();
        return view('users.edit', compact('user', 'roles'));
    }

    public function update(Request $request, User $user)
    {
        // Validate the request
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => [
                'required',
                'email',
                Rule::unique('users')->ignore($user->id),
            ],
            'password' => 'nullable|string|min:8|confirmed',
            'role' => 'required|string|exists:roles,slug',
            'avatar' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'is_active' => 'sometimes|boolean'
        ]);

        // Capture old values before update
        $oldValues = $user->toArray();
        
        // Prepare update data
        $data = $request->except('password', 'avatar', 'password_confirmation');
        
        // Handle is_active - cast to integer
        $data['is_active'] = (int) $request->input('is_active', 0);
        
        // Handle password update if provided
        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
        }

        // Handle avatar upload
        if ($request->hasFile('avatar')) {
            // Delete old avatar if exists
            if ($user->avatar && Storage::disk('public')->exists($user->avatar)) {
                Storage::disk('public')->delete($user->avatar);
            }
            $data['avatar'] = $request->file('avatar')->store('avatars', 'public');
        }

        Log::info('Updating user', [
            'user_id' => $user->id,
            'data' => $data,
            'request_data' => $request->all()
        ]);

        // Update the user
        $updated = $user->update($data);
        
        if (!$updated) {
            Log::error('Failed to update user', ['user_id' => $user->id]);
            return redirect()->back()
                ->with('error', 'Failed to update user.')
                ->withInput();
        }

        // Refresh user to get new values
        $user->refresh();
        $newValues = $user->toArray();

        Log::info('User updated successfully', [
            'user_id' => $user->id,
            'old_values' => $oldValues,
            'new_values' => $newValues
        ]);

        // Log user update
        AuditLog::create([
            'action' => 'updated',
            'model_type' => User::class,
            'model_id' => $user->id,
            'old_values' => $oldValues,
            'new_values' => $newValues,
            'description' => "User {$user->name} was updated",
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'url' => $request->url(),
            'method' => $request->method(),
            'user_id' => auth()->id(),
        ]);

        return redirect()->route('users.index')
            ->with('success', 'User updated successfully.');
    }

    public function destroy(User $user)
    {
        if ($user->id === auth()->id()) {
            return redirect()->route('users.index')
                ->with('error', 'You cannot delete your own account.');
        }

        $oldValues = $user->toArray();
        $user->delete();

        // Log user deletion (soft delete)
        AuditLog::create([
            'action' => 'deleted',
            'model_type' => User::class,
            'model_id' => $user->id,
            'old_values' => $oldValues,
            'description' => "User {$user->name} was deleted",
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'url' => request()->url(),
            'method' => request()->method(),
            'user_id' => auth()->id(),
        ]);

        return redirect()->route('users.index')
            ->with('success', 'User deleted successfully.');
    }

    public function restore($id)
    {
        $user = User::withTrashed()->findOrFail($id);
        $user->restore();

        // Log user restoration
        AuditLog::create([
            'action' => 'restored',
            'model_type' => User::class,
            'model_id' => $user->id,
            'description' => "User {$user->name} was restored",
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'url' => request()->url(),
            'method' => request()->method(),
            'user_id' => auth()->id(),
        ]);

        return redirect()->route('users.index')
            ->with('success', 'User restored successfully.');
    }

    public function forceDelete($id)
    {
        $user = User::withTrashed()->findOrFail($id);
        
        if ($user->id === auth()->id()) {
            return redirect()->route('users.index')
                ->with('error', 'You cannot permanently delete your own account.');
        }

        // Delete avatar if exists
        if ($user->avatar && Storage::disk('public')->exists($user->avatar)) {
            Storage::disk('public')->delete($user->avatar);
        }

        $oldValues = $user->toArray();
        $user->forceDelete();

        // Log user permanent deletion
        AuditLog::create([
            'action' => 'force_deleted',
            'model_type' => User::class,
            'model_id' => $id,
            'old_values' => $oldValues,
            'description' => "User {$oldValues['name']} was permanently deleted",
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'url' => request()->url(),
            'method' => request()->method(),
            'user_id' => auth()->id(),
        ]);

        return redirect()->route('users.index')
            ->with('success', 'User permanently deleted.');
    }

    public function profile()
    {
        $user = auth()->user();
        return view('users.profile', compact('user'));
    }

    public function updateProfile(Request $request)
    {
        $user = auth()->user();
        
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => [
                'required',
                'email',
                Rule::unique('users')->ignore($user->id),
            ],
            'current_password' => 'required_with:password|current_password',
            'password' => 'nullable|string|min:8|confirmed',
            'avatar' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $oldValues = $user->toArray();
        
        $data = $request->except('password', 'avatar', 'current_password', 'password_confirmation');

        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
        }

        if ($request->hasFile('avatar')) {
            // Delete old avatar if exists
            if ($user->avatar && Storage::disk('public')->exists($user->avatar)) {
                Storage::disk('public')->delete($user->avatar);
            }
            $data['avatar'] = $request->file('avatar')->store('avatars', 'public');
        }

        $user->update($data);
        $newValues = $user->fresh()->toArray();

        // Log profile update
        AuditLog::create([
            'action' => 'updated',
            'model_type' => User::class,
            'model_id' => $user->id,
            'old_values' => $oldValues,
            'new_values' => $newValues,
            'description' => "User {$user->name} updated their profile",
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'url' => $request->url(),
            'method' => $request->method(),
            'user_id' => $user->id,
        ]);

        return redirect()->route('profile')
            ->with('success', 'Profile updated successfully.');
    }
}