<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Enums\Role;
use Illuminate\Http\Request;
use App\Http\Requests\StoreUserRequest;
use App\Http\Requests\UpdateUserRequest;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): View
    {
        $this->authorize('manage-users');
        
        $usersQuery = User::query();
        
        // Apply user-based filtering
        if (Auth::check()) {
            $currentUser = Auth::user();
            
            // If user is not super admin, filter users they can manage
            if (!$currentUser->isSuperAdmin()) {
                $usersQuery->where(function($query) use ($currentUser) {
                    // Users can see themselves and users with lower roles
                    $query->where('id', $currentUser->id)
                          ->orWhere('role', '<', $currentUser->role->value);
                });
            }
        } else {
            // Guests shouldn't see any users (but manage-users policy should prevent access anyway)
            $usersQuery->where('id', 0);
        }
        
        $users = $usersQuery->latest()->paginate(10);
        return view('users.index', compact('users'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        $this->authorize('manage-users');
        
        $currentUser = Auth::user();
        $roles = Role::all();
        
        // Filter roles based on current user's permissions
        if (!$currentUser->isSuperAdmin()) {
            $roles = $roles->filter(function($role) use ($currentUser) {
                return $role->value < $currentUser->role->value;
            });
        }
        
        return view('users.create', compact('roles'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreUserRequest $request): RedirectResponse
    {
        $this->authorize('manage-users');
        
        $currentUser = Auth::user();
        $requestedRole = $request->role;
        
        // Validate that user can assign the requested role
        if (!$currentUser->isSuperAdmin() && $requestedRole >= $currentUser->role->value) {
            return redirect()->back()
                ->with('error', 'You cannot create users with equal or higher role than yourself.')
                ->withInput();
        }
        
        User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => $requestedRole,
        ]);

        return redirect()->route('users.index')
                        ->with('success', 'User created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(User $user): View
    {
        $this->authorize('manage-users');
        
        // Check if current user can view this specific user
        $this->checkUserAccess($user);
        
        return view('users.show', compact('user'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(User $user): View
    {
        $this->authorize('manage-users');
        
        // Check if current user can edit this specific user
        $this->checkUserAccess($user);
        
        $currentUser = Auth::user();
        $roles = Role::all();
        
        // Filter roles based on current user's permissions
        if (!$currentUser->isSuperAdmin()) {
            $roles = $roles->filter(function($role) use ($currentUser) {
                return $role->value < $currentUser->role->value;
            });
        }
        
        return view('users.edit', compact('user', 'roles'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateUserRequest $request, User $user): RedirectResponse
    {
        $this->authorize('manage-users');
        
        // Check if current user can update this specific user
        $this->checkUserAccess($user);
        
        $currentUser = Auth::user();
        $requestedRole = $request->role;
        
        // Validate role assignment permissions
        if (!$currentUser->isSuperAdmin()) {
            // Cannot change role to equal or higher than current user
            if ($requestedRole >= $currentUser->role->value) {
                return redirect()->back()
                    ->with('error', 'You cannot assign a role equal or higher than your own.')
                    ->withInput();
            }
            
            // Cannot change role of users with equal or higher role
            if ($user->role->value >= $currentUser->role->value) {
                return redirect()->back()
                    ->with('error', 'You cannot modify users with equal or higher role than yourself.')
                    ->withInput();
            }
        }

        $data = [
            'name' => $request->name,
            'email' => $request->email,
            'role' => $requestedRole,
        ];

        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
        }

        $user->update($data);

        return redirect()->route('users.index')
                        ->with('success', 'User updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(User $user): RedirectResponse
    {
        $this->authorize('manage-users');
        
        // Check if current user can delete this specific user
        $this->checkUserAccess($user);

        if ($user->id === auth()->id()) {
            return redirect()->route('users.index')
                            ->with('error', 'You cannot delete your own account.');
        }

        $user->delete();

        return redirect()->route('users.index')
                        ->with('success', 'User deleted successfully.');
    }

    /**
     * Check if current user has access to manage the target user
     */
    private function checkUserAccess(User $targetUser): void
    {
        $currentUser = Auth::user();
        
        // Super admins can manage anyone
        if ($currentUser->isSuperAdmin()) {
            return;
        }
        
        // Users can only manage users with lower roles or themselves
        if ($targetUser->role->value >= $currentUser->role->value && $targetUser->id !== $currentUser->id) {
            abort(403, 'You do not have permission to manage this user.');
        }
    }
    
    /**
     * User profile - users can view and edit their own profile
     */
    public function profile(): View
    {
        $user = Auth::user();
        return view('users.profile', compact('user'));
    }
    
    /**
     * Update user profile
     */
    public function updateProfile(Request $request): RedirectResponse
    {
        $user = Auth::user();
        
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'current_password' => 'nullable|required_with:password|current_password',
            'password' => 'nullable|min:8|confirmed',
        ]);
        
        $data = [
            'name' => $validated['name'],
            'email' => $validated['email'],
        ];
        
        if (!empty($validated['password'])) {
            $data['password'] = Hash::make($validated['password']);
        }
        
        $user->update($data);
        
        return redirect()->route('profile')
                        ->with('success', 'Profile updated successfully.');
    }
}