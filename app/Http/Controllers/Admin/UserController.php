<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $query = User::query()->orderByDesc('created_at');

        if ($q = $request->input('q')) {
            $query->where(function($sub) use ($q) {
                $sub->where('name', 'like', "%{$q}%")
                    ->orWhere('email', 'like', "%{$q}%");
            });
        }

        $users = $query->paginate(20)->withQueryString();

        return view('admin.users.index', compact('users'));
    }

    public function create()
    {
        return view('admin.users.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'string', 'min:6'],
            'role' => ['required', 'in:admin,editor'],
            'is_active' => ['boolean'],
        ]);

        User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'role' => $validated['role'],
            'is_active' => $request->has('is_active') ? 1 : 0,
        ]);

        return redirect()->route('admin.users.index')
            ->with('toast', ['title' => 'User created successfully', 'tone' => 'success']);
    }

    public function edit(User $user)
    {
        return view('admin.users.edit', compact('user'));
    }

    public function update(Request $request, User $user)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users')->ignore($user->id)],
            'role' => ['required', 'in:admin,editor'],
            'is_active' => ['boolean'],
            'password' => ['nullable', 'string', 'min:6'],
        ]);

        // Security: Prevent disabling yourself
        if ($user->id === auth()->id()) {
             // If trying to disable self, force active
             if (!$request->has('is_active')) {
                 return back()->with('toast', ['title' => 'You cannot disable your own account', 'tone' => 'danger']);
             }
             // If trying to demote self (optional rule, but sticking to basics)
        }

        $updateData = [
            'name' => $validated['name'],
            'email' => $validated['email'],
            'role' => $validated['role'],
            // Checkbox handling: if present -> 1, else 0
            'is_active' => $request->has('is_active') ? 1 : 0,
        ];

        if (!empty($validated['password'])) {
            $updateData['password'] = Hash::make($validated['password']);
        }

        $user->update($updateData);

        return redirect()->route('admin.users.index')
            ->with('toast', ['title' => 'User updated successfully', 'tone' => 'success']);
    }

    // Optional: dedicated toggle route
    public function toggleActive(User $user)
    {
        if ($user->id === auth()->id()) {
            return back()->with('toast', ['title' => 'Cannot disable yourself', 'tone' => 'danger']);
        }

        $user->update(['is_active' => !$user->is_active]);

        $status = $user->is_active ? 'enabled' : 'disabled';
        return back()->with('toast', ['title' => "User {$status}", 'tone' => 'success']);
    }
}
