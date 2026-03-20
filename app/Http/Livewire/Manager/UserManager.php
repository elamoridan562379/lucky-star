<?php

namespace App\Http\Livewire\Manager;

use App\Models\User;
use App\Services\UserManagementService;
use Illuminate\Support\Facades\Hash;
<<<<<<< HEAD
use Illuminate\Support\Str;
=======
>>>>>>> 17e9c9617d7de32f80264abdd22516d36dfc6413
use Livewire\Component;
use Livewire\WithPagination;

class UserManager extends Component
{
    use WithPagination;

    public bool $showModal = false;
    public bool $showDeleteModal = false;
<<<<<<< HEAD
    public bool $showResetModal = false;

    public ?int $editId = null;
    public ?int $deleteId = null;
    public ?int $resetId = null;

    public string $resetUserName = '';
    public string $temporaryPassword = '';

=======
    public ?int $editId = null;
    public ?int $deleteId = null;
>>>>>>> 17e9c9617d7de32f80264abdd22516d36dfc6413
    public string $name = '';
    public string $email = '';
    public string $password = '';
    public string $role = 'cashier';
    public string $status = 'active';
    public string $search = '';

    protected $userManagementService;

    public function boot(UserManagementService $userManagementService)
    {
        $this->userManagementService = $userManagementService;
    }

    protected function rules(): array
    {
        $unique = $this->editId
            ? "unique:users,email,{$this->editId}"
            : 'unique:users,email';

<<<<<<< HEAD
        $emailRule = $this->role === 'admin'
            ? "required|email|{$unique}"
            : "nullable|email|{$unique}";

        return [
            'name' => 'required|string|max:200',
            'email' => $emailRule,
=======
        return [
            'name' => 'required|string|max:200',
            'email' => "required|email|{$unique}",
>>>>>>> 17e9c9617d7de32f80264abdd22516d36dfc6413
            'password' => $this->editId ? 'nullable|min:8' : 'required|min:8',
            'role' => 'required|in:' . implode(',', array_keys($this->getAvailableRolesForCreation())),
            'status' => 'required|in:active,inactive',
        ];
    }

    public function getAvailableRolesForCreation(): array
    {
        return $this->userManagementService->getAvailableRolesForCreation(auth()->user()->role);
    }

    public function getAvailableRolesForEditing(): array
    {
<<<<<<< HEAD
        if (! $this->editId) {
            return [];
        }

=======
        if (!$this->editId) {
            return [];
        }
>>>>>>> 17e9c9617d7de32f80264abdd22516d36dfc6413
        return $this->userManagementService->getAvailableRolesForEditing(auth()->user()->role, $this->editId);
    }

    public function getCanDeleteUsers(): bool
    {
        return $this->userManagementService->canDeleteUser(auth()->user()->role, null);
    }

    public function getCanEditUser($userId): bool
    {
        return $this->userManagementService->canEditUser(auth()->user()->role, $userId);
    }

<<<<<<< HEAD
    public function canResetUser($user): bool
    {
        if (auth()->user()->role !== 'admin') {
            return false;
        }

        if ($user->id === auth()->id()) {
            return false;
        }

        if ($user->role === 'admin') {
            return false;
        }

        return true;
    }

=======
>>>>>>> 17e9c9617d7de32f80264abdd22516d36dfc6413
    public function openCreate(): void
    {
        $this->resetForm();
        $this->showModal = true;
    }

    public function openEdit(int $id): void
    {
<<<<<<< HEAD
        if (! $this->getCanEditUser($id)) {
=======
        if (!$this->getCanEditUser($id)) {
>>>>>>> 17e9c9617d7de32f80264abdd22516d36dfc6413
            session()->flash('error', 'You do not have permission to edit this user.');
            return;
        }

        $user = User::findOrFail($id);
<<<<<<< HEAD

        $this->editId = $id;
        $this->name = $user->name;
        $this->email = $user->email ?? '';
        $this->role = $user->role;
        $this->status = $user->status ?? 'active';
        $this->password = '';
        $this->search = '';
=======
        $this->editId = $id;
        $this->name = $user->name;
        $this->email = $user->email;
        $this->role = $user->role;
        $this->status = $user->status ?? 'active';
        $this->password = '';
        $this->search = ''; // Clear search when editing
>>>>>>> 17e9c9617d7de32f80264abdd22516d36dfc6413
        $this->showModal = true;
    }

    public function openDelete(int $id): void
    {
<<<<<<< HEAD
        if (! $this->getCanDeleteUsers()) {
=======
        if (!$this->getCanDeleteUsers()) {
>>>>>>> 17e9c9617d7de32f80264abdd22516d36dfc6413
            session()->flash('error', 'You do not have permission to delete users.');
            return;
        }

        $this->deleteId = $id;
        $this->showDeleteModal = true;
    }

<<<<<<< HEAD
    public function openReset(int $id): void
    {
        $user = User::findOrFail($id);

        if (! $this->canResetUser($user)) {
            session()->flash('error', 'You cannot reset this account.');
            return;
        }

        $this->resetId = $user->id;
        $this->resetUserName = $user->name;
        $this->temporaryPassword = '';
        $this->showResetModal = true;
    }

    public function resetUserPassword(): void
    {
        if (! $this->resetId) {
            return;
        }

        $user = User::findOrFail($this->resetId);

        if (! $this->canResetUser($user)) {
            session()->flash('error', 'You cannot reset this account.');
            $this->showResetModal = false;
            $this->resetId = null;
            return;
        }

        $tempPassword = 'Temp' . Str::upper(Str::random(6)) . rand(10, 99);

        $user->update([
            'password' => Hash::make($tempPassword),
        ]);

        $this->userManagementService->logUserAction(auth()->id(), 'reset_password', [
            'reset_user_id' => $user->id,
            'reset_user_name' => $user->name,
        ]);

        $this->temporaryPassword = $tempPassword;
        session()->flash('success', "Password reset successful for {$user->name}. Temporary password: {$tempPassword}");

        $this->showResetModal = false;
        $this->resetId = null;
        $this->resetUserName = '';
    }

    public function delete(): void
    {
        if ($this->deleteId && $this->deleteId !== auth()->id() && $this->getCanDeleteUsers()) {
            $user = User::findOrFail($this->deleteId);
            $deletedName = $user->name;
            $user->delete();

            $this->userManagementService->logUserAction(auth()->id(), 'delete', [
                'deleted_user_id' => $this->deleteId,
                'deleted_user_name' => $deletedName,
            ]);

            session()->flash('success', 'User deleted successfully.');
        } elseif (! $this->getCanDeleteUsers()) {
            session()->flash('error', 'Only administrators can delete users.');
        }

=======
    public function delete(): void
    {
        if ($this->deleteId && $this->deleteId !== auth()->id() && $this->getCanDeleteUsers()) {
            User::findOrFail($this->deleteId)->delete();
            $this->userManagementService->logUserAction(auth()->id(), 'delete', [
                'deleted_user_id' => $this->deleteId,
                'deleted_user_name' => User::withTrashed()->find($this->deleteId)->name
            ]);
            session()->flash('success', 'User deleted successfully.');
        } elseif (!$this->getCanDeleteUsers()) {
            session()->flash('error', 'Only administrators can delete users.');
        }
>>>>>>> 17e9c9617d7de32f80264abdd22516d36dfc6413
        $this->showDeleteModal = false;
        $this->deleteId = null;
    }

    public function save(): void
    {
        $this->validate();

<<<<<<< HEAD
        if (! $this->editId && ! $this->userManagementService->canCreateUser(auth()->user()->role, $this->role)) {
=======
        // Check if user can create this role
        if (!$this->editId && !$this->userManagementService->canCreateUser(auth()->user()->role, $this->role)) {
>>>>>>> 17e9c9617d7de32f80264abdd22516d36dfc6413
            session()->flash('error', 'You do not have permission to create users with this role.');
            return;
        }

<<<<<<< HEAD
        if ($this->editId && ! $this->getCanEditUser($this->editId)) {
=======
        // Check if user can edit this user
        if ($this->editId && !$this->getCanEditUser($this->editId)) {
>>>>>>> 17e9c9617d7de32f80264abdd22516d36dfc6413
            session()->flash('error', 'You do not have permission to edit this user.');
            return;
        }

        if ($this->editId) {
            $data = [
                'name' => $this->name,
<<<<<<< HEAD
                'email' => $this->email !== '' ? $this->email : null,
                'status' => $this->status,
            ];

            if ($this->userManagementService->canChangeRole(auth()->user()->role, $this->role)) {
                $data['role'] = $this->role;
            }

            if ($this->password) {
                $data['password'] = Hash::make($this->password);
            }

            User::findOrFail($this->editId)->update($data);

            $this->userManagementService->logUserAction(auth()->id(), 'edit', [
                'edited_user_id' => $this->editId,
                'changes' => $data,
            ]);

=======
                'email' => $this->email,
                'status' => $this->status,
            ];
            
            // Only admins can change roles
            if ($this->userManagementService->canChangeRole(auth()->user()->role, $this->role)) {
                $data['role'] = $this->role;
            }
            
            if ($this->password) {
                $data['password'] = Hash::make($this->password);
            }
            
            User::findOrFail($this->editId)->update($data);
            $this->userManagementService->logUserAction(auth()->id(), 'edit', [
                'edited_user_id' => $this->editId,
                'changes' => $data
            ]);
>>>>>>> 17e9c9617d7de32f80264abdd22516d36dfc6413
            session()->flash('success', 'User updated.');
        } else {
            User::create([
                'name' => $this->name,
<<<<<<< HEAD
                'email' => $this->email !== '' ? $this->email : null,
=======
                'email' => $this->email,
>>>>>>> 17e9c9617d7de32f80264abdd22516d36dfc6413
                'password' => Hash::make($this->password),
                'role' => $this->role,
                'status' => $this->status,
            ]);
<<<<<<< HEAD

            $this->userManagementService->logUserAction(auth()->id(), 'create', [
                'created_user_name' => $this->name,
                'created_user_role' => $this->role,
            ]);

=======
            $this->userManagementService->logUserAction(auth()->id(), 'create', [
                'created_user_name' => $this->name,
                'created_user_role' => $this->role
            ]);
>>>>>>> 17e9c9617d7de32f80264abdd22516d36dfc6413
            session()->flash('success', 'User created.');
        }

        $this->showModal = false;
        $this->resetForm();
    }

    private function resetForm(): void
    {
        $this->editId = null;
        $this->deleteId = null;
<<<<<<< HEAD
        $this->resetId = null;
        $this->resetUserName = '';
        $this->temporaryPassword = '';

=======
>>>>>>> 17e9c9617d7de32f80264abdd22516d36dfc6413
        $this->name = '';
        $this->email = '';
        $this->password = '';
        $this->role = 'cashier';
        $this->status = 'active';
<<<<<<< HEAD
        $this->search = '';

=======
        $this->search = ''; // Clear search on reset
>>>>>>> 17e9c9617d7de32f80264abdd22516d36dfc6413
        $this->resetValidation();
    }

    public function render()
    {
        $query = $this->userManagementService->getUsersForManagement(auth()->user()->role);

        if ($this->search) {
<<<<<<< HEAD
            $query->where(function ($q) {
                $q->where('name', 'like', '%' . $this->search . '%')
                    ->orWhere('email', 'like', '%' . $this->search . '%')
                    ->orWhere('role', 'like', '%' . $this->search . '%');
=======
            $query->where(function($q) {
                $q->where('name', 'like', '%' . $this->search . '%')
                  ->orWhere('email', 'like', '%' . $this->search . '%')
                  ->orWhere('role', 'like', '%' . $this->search . '%');
>>>>>>> 17e9c9617d7de32f80264abdd22516d36dfc6413
            });
        }

        return view('livewire.manager.user-manager', [
            'users' => $query->latest()->paginate(20),
        ])->layout('layouts.manager');
    }
}
