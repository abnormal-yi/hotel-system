<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    public function index()
    {
        $query = User::orderBy('role')->orderBy('name');
        if (auth()->user()->role === 'manager') {
            $query->whereIn('role', ['receptionist', 'manager']);
        }
        $users = $query->paginate(20);
        return view('users.index', compact('users'));
    }

    public function create()
    {
        return view('users.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email',
            'password' => 'required|string|min:6|confirmed',
            'role' => ['required', Rule::in(['receptionist', 'manager'])],
        ]);

        User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'role' => $validated['role'],
            'is_active' => true,
        ]);

        activity()->causedBy(auth()->user())->log('Created user: ' . $validated['name'] . ' (' . $validated['role'] . ')');

        return redirect()->route('users.index')->with('success', 'User created successfully.');
    }

    public function edit(User $user)
    {
        return view('users.edit', compact('user'));
    }

    public function update(Request $request, User $user)
    {
        $rules = [
            'name' => 'required|string|max:255',
            'email' => ['required', 'email', 'max:255', Rule::unique('users')->ignore($user->id)],
            'password' => 'nullable|string|min:6|confirmed',
            'is_active' => 'boolean',
        ];

        if (auth()->user()->role === 'creator' || $user->role !== 'creator') {
            $rules['role'] = ['required', Rule::in(['receptionist', 'manager', 'creator'])];
        }

        $validated = $request->validate($rules);

        $updateData = [
            'name' => $validated['name'],
            'email' => $validated['email'],
            'is_active' => $request->boolean('is_active'),
        ];

        if (isset($validated['role'])) {
            $updateData['role'] = $validated['role'];
        }

        if (!empty($validated['password'])) {
            $updateData['password'] = Hash::make($validated['password']);
        }

        $user->update($updateData);

        activity()->causedBy(auth()->user())->log('Updated user: ' . $user->name);

        return redirect()->route('users.index')->with('success', 'User updated successfully.');
    }
}
