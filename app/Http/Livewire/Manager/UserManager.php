<?php

namespace App\Http\Livewire\Manager;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Livewire\Component;
use Livewire\WithPagination;

class UserManager extends Component
{
    use WithPagination;

    public bool   $showModal = false;
    public bool   $showDeleteModal = false;
    public ?int   $editId    = null;
    public ?int   $deleteId  = null;
    public string $name      = '';
    public string $email     = '';
    public string $password  = '';
    public string $role      = 'cashier';
    public string $status    = 'active';
    public string $search    = '';

    protected function rules(): array
    {
        $unique = $this->editId
            ? "unique:users,email,{$this->editId}"
            : 'unique:users,email';

        return [
            'name'     => 'required|string|max:200',
            'email'    => "required|email|{$unique}",
            'password' => $this->editId ? 'nullable|min:8' : 'required|min:8',
            'role'     => 'required|in:cashier,manager,inventory_clerk,admin',
            'status'   => 'required|in:active,inactive',
        ];
    }

    public function openCreate(): void
    {
        $this->resetForm();
        $this->showModal = true;
    }

    public function openEdit(int $id): void
    {
        $user           = User::findOrFail($id);
        $this->editId   = $id;
        $this->name     = $user->name;
        $this->email    = $user->email;
        $this->role     = $user->role;
        $this->status   = $user->status ?? 'active';
        $this->password = '';
        $this->search    = ''; // Clear search when editing
        $this->showModal = true;
    }

    public function openDelete(int $id): void
    {
        $this->deleteId = $id;
        $this->showDeleteModal = true;
    }

    public function delete(): void
    {
        if ($this->deleteId && $this->deleteId !== auth()->id() && auth()->user()->isAdmin()) {
            User::findOrFail($this->deleteId)->delete();
            session()->flash('success', 'User deleted successfully.');
        } elseif (!auth()->user()->isAdmin()) {
            session()->flash('error', 'Only administrators can delete users.');
        }
        $this->showDeleteModal = false;
        $this->deleteId = null;
    }

    public function save(): void
    {
        $this->validate();

        // Restrict manager creation to admins only
        if ($this->role === 'manager' && !auth()->user()->isAdmin()) {
            session()->flash('error', 'Only administrators can create manager accounts.');
            return;
        }

        if ($this->editId) {
            $data = [
                'name'   => $this->name,
                'email'  => $this->email,
                'role'   => $this->role,
                'status' => $this->status,
            ];
            if ($this->password) {
                $data['password'] = Hash::make($this->password);
            }
            User::findOrFail($this->editId)->update($data);
            session()->flash('success', 'User updated.');
        } else {
            User::create([
                'name'     => $this->name,
                'email'    => $this->email,
                'password' => Hash::make($this->password),
                'role'     => $this->role,
                'status'   => $this->status,
            ]);
            session()->flash('success', 'User created.');
        }

        $this->showModal = false;
        $this->resetForm();
    }

    private function resetForm(): void
    {
        $this->editId   = null;
        $this->deleteId = null;
        $this->name     = '';
        $this->email    = '';
        $this->password = '';
        $this->role     = 'cashier';
        $this->status   = 'active';
        $this->search    = ''; // Clear search on reset
        $this->resetValidation();
    }

    public function render()
    {
        $query = User::query();

        if ($this->search) {
            $query->where(function($q) {
                $q->where('name', 'like', '%' . $this->search . '%')
                  ->orWhere('email', 'like', '%' . $this->search . '%')
                  ->orWhere('role', 'like', '%' . $this->search . '%');
            });
        }

        return view('livewire.manager.user-manager', [
            'users' => $query->orderBy('role')->orderBy('name')->paginate(20),
        ])->layout('layouts.manager');
    }
}
