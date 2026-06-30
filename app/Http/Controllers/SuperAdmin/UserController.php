<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Http\Requests\SuperAdmin\StoreUserRequest;
use App\Http\Requests\SuperAdmin\UpdateUserRequest;
use App\Models\User;
use App\Services\SuperAdmin\UserService;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function __construct(
        protected UserService $userService
    ) {}

    public function index(Request $request)
    {
        $users = $this->userService->getAll(
            $request->only('search', 'role', 'status')
        );

        $roles = $this->userService->roles();
        $statuses = $this->userService->statuses();

        return view('super-admin.users.index', compact('users', 'roles', 'statuses'));
    }

    public function create()
    {
        $roles = $this->userService->roles();

        return view('super-admin.users.create', compact('roles'));
    }

    public function store(StoreUserRequest $request)
    {
        $this->userService->create($request->validated());

        return redirect()
            ->route('super-admin.users.index')
            ->with('success', 'Pengguna berhasil ditambahkan.');
    }

    public function edit(User $user)
    {
        abort_if($user->role === 'super_admin', 403);

        $roles = $this->userService->roles();

        return view('super-admin.users.edit', compact('user', 'roles'));
    }

    public function update(UpdateUserRequest $request, User $user)
    {
        abort_if($user->role === 'super_admin', 403);

        $this->userService->update($user, $request->validated());

        return redirect()
            ->route('super-admin.users.index')
            ->with('success', 'Pengguna berhasil diperbarui. Jika reset password dicentang, password default adalah roti12345.');
    }

    public function updateStatus(User $user)
    {
        abort_if($user->role === 'super_admin', 403);

        $user->update([
            'status' => $user->status === 'aktif' ? 'nonaktif' : 'aktif',
        ]);

        $message = $user->status === 'aktif'
            ? 'Pengguna berhasil diaktifkan.'
            : 'Pengguna berhasil dinonaktifkan.';

        return redirect()
            ->back()
            ->with('success', $message);
    }

    public function destroy(User $user)
    {
        abort(403);
    }
}