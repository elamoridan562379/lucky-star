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
    public ?int   $editId    = null;
    public string $name      = '';
    public string $email     = '';
    public string $password  = '';
    public string $role      = 'cashier';

    protected function rules(): array
    {
        $unique = $this->editId
            ? "unique:users,email,{$this->editId}"
            : 'unique:users,email';

        return [
            'name'     => 'required|string|max:200',
            'email'    => "required|email|{$unique}",
            'password' => $this->editId ? 'nullable|min:8' : 'required|min:8',
            'role'     => 'required|in:cashier,manager',
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
        $this->password = '';
        $this->showModal = true;
    }

    public function save(): void
    {
        $this->validate();

        if ($this->editId) {
            $data = [
                'name'  => $this->name,
                'email' => $this->email,
                'role'  => $this->role,
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
            ]);
            session()->flash('success', 'User created.');
        }

        $this->showModal = false;
        $this->resetForm();
    }

    private function resetForm(): void
    {
        $this->editId   = null;
        $this->name     = '';
        $this->email    = '';
        $this->password = '';
        $this->role     = 'cashier';
        $this->resetValidation();
    }

    public function render()
    {
        return view('livewire.manager.user-manager', [
            'users' => User::orderBy('role')->orderBy('name')->paginate(20),
        ])->layout('layouts.manager');
    }
}
