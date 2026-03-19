<?php

namespace App\Http\Livewire\Manager;

use App\Models\User;
use App\Services\UserManagementService;
use Illuminate\Support\Facades\Hash;
use Livewire\Component;
use Livewire\WithPagination;

class UserManager extends Component
{
    use WithPagination;

    public bool $showModal = false;
    public bool $showDeleteModal = false;
    public ?int $editId = null;
    public ?int $deleteId = null;
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

        return [
            'name' => 'required|string|max:200',
            'email' => "required|email|{$unique}",
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
        if (!$this->editId) {
            return [];
        }
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

    public function openCreate(): void
    {
        $this->resetForm();
        $this->showModal = true;
    }

    public function openEdit(int $id): void
    {
        if (!$this->getCanEditUser($id)) {
            session()->flash('error', 'You do not have permission to edit this user.');
            return;
        }

        $user = User::findOrFail($id);
        $this->editId = $id;
        $this->name = $user->name;
        $this->email = $user->email;
        $this->role = $user->role;
        $this->status = $user->status ?? 'active';
        $this->password = '';
        $this->search = ''; // Clear search when editing
        $this->showModal = true;
    }

    public function openDelete(int $id): void
    {
        if (!$this->getCanDeleteUsers()) {
            session()->flash('error', 'You do not have permission to delete users.');
            return;
        }

        $this->deleteId = $id;
        $this->showDeleteModal = true;
    }

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
        $this->showDeleteModal = false;
        $this->deleteId = null;
    }

    public function save(): void
    {
        $this->validate();

        // Check if user can create this role
        if (!$this->editId && !$this->userManagementService->canCreateUser(auth()->user()->role, $this->role)) {
            session()->flash('error', 'You do not have permission to create users with this role.');
            return;
        }

        // Check if user can edit this user
        if ($this->editId && !$this->getCanEditUser($this->editId)) {
            session()->flash('error', 'You do not have permission to edit this user.');
            return;
        }

        if ($this->editId) {
            $data = [
                'name' => $this->name,
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
            session()->flash('success', 'User updated.');
        } else {
            User::create([
                'name' => $this->name,
                'email' => $this->email,
                'password' => Hash::make($this->password),
                'role' => $this->role,
                'status' => $this->status,
            ]);
            $this->userManagementService->logUserAction(auth()->id(), 'create', [
                'created_user_name' => $this->name,
                'created_user_role' => $this->role
            ]);
            session()->flash('success', 'User created.');
        }

        $this->showModal = false;
        $this->resetForm();
    }

    private function resetForm(): void
    {
        $this->editId = null;
        $this->deleteId = null;
        $this->name = '';
        $this->email = '';
        $this->password = '';
        $this->role = 'cashier';
        $this->status = 'active';
        $this->search = ''; // Clear search on reset
        $this->resetValidation();
    }

    public function render()
    {
        $query = $this->userManagementService->getUsersForManagement(auth()->user()->role);

        if ($this->search) {
            $query->where(function($q) {
                $q->where('name', 'like', '%' . $this->search . '%')
                  ->orWhere('email', 'like', '%' . $this->search . '%')
                  ->orWhere('role', 'like', '%' . $this->search . '%');
            });
        }

        return view('livewire.manager.user-manager', [
            'users' => $query->latest()->paginate(20),
        ])->layout('layouts.manager');
    }
}
