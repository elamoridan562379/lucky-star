<div>
    <div style="display:flex; align-items:center; justify-content:space-between; margin-bottom:1.5rem;">
        <h1 class="page-title" style="margin-bottom:0;">Users</h1>
        <button wire:click="openCreate" class="btn-primary">+ Add User</button>
    </div>

    <div class="card">
        <table class="data-table">
            <thead>
                <tr>
                    <th>Name</th>
                    <th>Email</th>
                    <th style="text-align:center;">Role</th>
                    <th style="text-align:center;">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($users as $user)
                <tr wire:key="user-{{ $user->id }}">
                    <td style="font-weight:700; font-family:'Playfair Display',serif; font-size:0.88rem;">{{ $user->name }}</td>
                    <td style="color:#7a5c44; font-size:0.8rem;">{{ $user->email }}</td>
                    <td style="text-align:center;">
                        <span class="badge {{ $user->role === 'manager' ? 'badge-manager' : 'badge-cashier' }}">
                            {{ ucfirst($user->role) }}
                        </span>
                    </td>
                    <td style="text-align:center;">
                        @if ($user->id !== auth()->id())
                            <button wire:click="openEdit({{ $user->id }})" class="btn-link">Edit</button>
                        @else
                            <span style="font-size:0.72rem; color:#c8b0a0;">You</span>
                        @endif
                    </td>
                </tr>
                @empty
                <tr><td colspan="4" style="text-align:center; padding:3rem; color:#c8b0a0; font-style:italic; font-family:'Playfair Display',serif;">No users found.</td></tr>
                @endforelse
            </tbody>
        </table>
        <div style="padding:1rem 1.25rem; border-top:1px solid rgba(74,37,24,0.06);">{{ $users->links() }}</div>
    </div>

    @if ($showModal)
    <div class="modal-bg">
        <div class="modal-box">
            <div class="modal-header">
                <span class="modal-title">{{ $editId ? 'Edit User' : 'New User' }}</span>
                <button wire:click="$set('showModal', false)" class="modal-close">✕</button>
            </div>
            <div class="modal-body" style="display:flex; flex-direction:column; gap:1rem;">
                <div>
                    <label class="form-label">Name *</label>
                    <input wire:model="name" type="text" class="form-input">
                    @error('name')<span class="form-error">{{ $message }}</span>@enderror
                </div>
                <div>
                    <label class="form-label">Email *</label>
                    <input wire:model="email" type="email" class="form-input">
                    @error('email')<span class="form-error">{{ $message }}</span>@enderror
                </div>
                <div>
                    <label class="form-label">Password {{ $editId ? '(leave blank to keep current)' : '*' }}</label>
                    <input wire:model="password" type="password" class="form-input">
                    @error('password')<span class="form-error">{{ $message }}</span>@enderror
                </div>
                <div>
                    <label class="form-label">Role *</label>
                    <select wire:model="role" class="form-input">
                        <option value="cashier">Cashier</option>
                        <option value="manager">Manager</option>
                    </select>
                </div>
            </div>
            <div class="modal-footer">
                <button wire:click="$set('showModal', false)" class="btn-secondary">Cancel</button>
                <button wire:click="save" class="btn-primary">{{ $editId ? 'Update' : 'Create' }}</button>
            </div>
        </div>
    </div>
    @endif
</div>
