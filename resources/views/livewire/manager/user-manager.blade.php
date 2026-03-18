<div>
    <div style="display:flex; align-items:center; justify-content:space-between; margin-bottom:1.5rem; gap:1rem;">
        <h1 class="page-title" style="margin-bottom:0;">Users</h1>
        <div style="display:flex; gap:1rem; align-items:center;">
            <input wire:model.live.debounce.300ms="search" 
                   type="text" 
                   placeholder="Search users..."
                   autocomplete="off"
                   style="padding:0.5rem 1rem; border:1px solid rgba(74,37,24,0.2); border-radius:8px; font-size:0.85rem; width:250px;">
            <button wire:click="openCreate" class="btn-primary">+ Add User</button>
        </div>
    </div>

    <div class="card">
        <table class="data-table">
            <thead>
                <tr>
                    <th>Name</th>
                    <th>Email</th>
                    <th style="text-align:center;">Role</th>
                    <th style="text-align:center;">Status</th>
                    <th style="text-align:center;">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($users as $user)
                <tr wire:key="user-{{ $user->id }}">
                    <td style="font-weight:700; font-family:'Playfair Display',serif; font-size:0.88rem; {{ $user->status === 'inactive' ? 'color:#c8b0a0;' : '' }}">{{ $user->name }}</td>
                    <td style="color:#7a5c44; font-size:0.8rem; {{ $user->status === 'inactive' ? 'color:#c8b0a0;' : '' }}">{{ $user->email }}</td>
                    <td style="text-align:center;">
                        <span class="badge {{ $user->role === 'manager' ? 'badge-manager' : ($user->role === 'inventory_clerk' ? 'badge-inventory' : 'badge-cashier') }}">
                            {{ ucfirst($user->role) }}
                        </span>
                    </td>
                    <td style="text-align:center;">
                        <span class="badge {{ $user->status === 'active' ? 'badge-active' : 'badge-inactive' }}">
                            {{ ucfirst($user->status) }}
                        </span>
                    </td>
                    <td style="text-align:center;">
                        @if ($user->id !== auth()->id())
                            <button wire:click="openEdit({{ $user->id }})" class="btn-link">Edit</button>
                            @if (auth()->user()->isAdmin())
                                <button wire:click="openDelete({{ $user->id }})" class="btn-link btn-delete" style="color:#e05252;">Delete</button>
                            @endif
                        @else
                            <span style="font-size:0.72rem; color:#c8b0a0;">You</span>
                        @endif
                    </td>
                </tr>
                @empty
                <tr><td colspan="5" style="text-align:center; padding:3rem; color:#c8b0a0; font-style:italic; font-family:'Playfair Display',serif;">No users found.</td></tr>
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
                    <input wire:model="email" type="email" autocomplete="off" class="form-input">
                    @error('email')<span class="form-error">{{ $message }}</span>@enderror
                </div>
                <div>
                    <label class="form-label">Password {{ $editId ? '(leave blank to keep current)' : '*' }}</label>
                    <input wire:model="password" type="password" autocomplete="new-password" class="form-input">
                    @error('password')<span class="form-error">{{ $message }}</span>@enderror
                </div>
                <div>
                    <label class="form-label">Role *</label>
                    <select wire:model="role" class="form-input">
                        <option value="cashier">Cashier</option>
                        <option value="inventory_clerk">Inventory Clerk</option>
                        @if (auth()->user()->isAdmin())
                            <option value="manager">Manager</option>
                        @endif
                    </select>
                </div>
                <div>
                    <label class="form-label">Status *</label>
                    <select wire:model="status" class="form-input">
                        <option value="active">Active</option>
                        <option value="inactive">Inactive</option>
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

    @if ($showDeleteModal)
    <div class="modal-bg">
        <div class="modal-box" style="max-width:400px;">
            <div class="modal-header">
                <span class="modal-title">Confirm Delete</span>
                <button wire:click="$set('showDeleteModal', false)" class="modal-close">✕</button>
            </div>
            <div class="modal-body">
                <p style="margin:0; color:#4a2518; font-size:0.95rem;">
                    Are you sure you want to delete this user? This action cannot be undone.
                </p>
            </div>
            <div class="modal-footer">
                <button wire:click="$set('showDeleteModal', false)" class="btn-secondary">Cancel</button>
                <button wire:click="delete" class="btn-primary" style="background:#e05252; border-color:#e05252;">Delete User</button>
            </div>
        </div>
    </div>
    @endif

    <style>
        .badge-inventory {
            background: #3d4a2e;
            color: #f5ead8;
        }
        .badge-active {
            background: #2d6a2d;
            color: white;
        }
        .badge-inactive {
            background: #8b7355;
            color: white;
        }
        .btn-delete {
            margin-left: 0.5rem;
        }
        .btn-delete:hover {
            background: rgba(224,82,82,0.1);
            border-radius: 4px;
        }
    </style>
</div>
