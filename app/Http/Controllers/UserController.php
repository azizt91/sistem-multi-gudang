<?php

namespace App\Http\Controllers;


use App\Models\User;
use App\Models\Warehouse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rules\Password;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $query = User::with('warehouse');

        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%')
                  ->orWhere('email', 'like', '%' . $request->search . '%');
            });
        }

        if ($request->filled('role')) {
            $query->where('role', $request->role);
        }

        $users = $query->orderBy('name')->paginate(10);

        return view('users.index', compact('users'));
    }

    public function create()
    {
        $roles = User::getRoles();
        $warehouses = Warehouse::orderBy('name')->get();
        return view('users.create', compact('roles', 'warehouses'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email',
            'password' => ['required', 'confirmed', Password::defaults()],
            'role' => 'required|in:admin,staff,owner',
            'warehouse_id' => 'required_if:role,staff|nullable|exists:warehouses,id',
        ]);

        // Force warehouse_id to null if not staff
        if ($validated['role'] !== User::ROLE_STAFF) {
            $validated['warehouse_id'] = null;
        }

        $validated['password'] = bcrypt($validated['password']);

        User::create($validated);

        return redirect()->route('users.index')
            ->with('success', 'User berhasil ditambahkan.');
    }

    public function edit(User $user)
    {
        $roles = User::getRoles();
        $warehouses = Warehouse::orderBy('name')->get();
        return view('users.edit', compact('user', 'roles', 'warehouses'));
    }

    public function update(Request $request, User $user)
    {
        $rules = [
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email,' . $user->id,
            'password' => ['nullable', 'confirmed', Password::defaults()],
            'role' => 'required|in:admin,staff,owner',
            'warehouse_id' => 'required_if:role,staff|nullable|exists:warehouses,id',
        ];

        $validated = $request->validate($rules);

        // Force warehouse_id to null if not staff
        if ($validated['role'] !== User::ROLE_STAFF) {
            $validated['warehouse_id'] = null;
        }

        if (empty($validated['password'])) {
            unset($validated['password']);
        } else {
            $validated['password'] = bcrypt($validated['password']);
        }

        $user->update($validated);

        return redirect()->route('users.index')
            ->with('success', 'User berhasil diperbarui.');
    }

    public function destroy(User $user)
    {
        // Prevent self-deletion
        if ($user->id === auth()->id()) {
            return redirect()->route('users.index')
                ->with('error', 'Anda tidak dapat menghapus akun sendiri.');
        }

        // Check if user has transactions
        if ($user->stockTransactions()->exists()) {
            return redirect()->route('users.index')
                ->with('error', 'User tidak dapat dihapus karena memiliki riwayat transaksi.');
        }

        $user->delete();

        return redirect()->route('users.index')
            ->with('success', 'User berhasil dihapus.');
    }
}
