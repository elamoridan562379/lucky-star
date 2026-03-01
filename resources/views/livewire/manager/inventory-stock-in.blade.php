<div>
    <div style="display:flex; align-items:center; justify-content:space-between; margin-bottom:1.5rem; flex-wrap:wrap; gap:1rem;">
        <h1 class="page-title" style="margin-bottom:0;">Stock In / Out</h1>
        <button wire:click="$toggle('showOutForm')"
            class="{{ $showOutForm ? 'btn-secondary' : 'btn-danger' }}">
            {{ $showOutForm ? '← Back to Stock In' : '↓ Manual Stock Out' }}
        </button>
    </div>

    @if ($successMsg)
    <div class="alert-success">✓ {{ $successMsg }}
        <button wire:click="$set('successMsg','')" style="margin-left:auto; background:none; border:none; cursor:pointer; color:#2d5a2d; font-size:1rem;">✕</button>
    </div>
    @endif

    <div style="display:grid; grid-template-columns:1fr 1fr; gap:1.25rem; align-items:start;">

        @if (!$showOutForm)
        {{-- Stock In Form --}}
        <div class="card">
            <div class="card-header">
                <span class="card-title">↑ Receive Stock</span>
            </div>
            <div style="padding:1.5rem; display:flex; flex-direction:column; gap:1rem;">
                @if ($errorMsg)
                    <div class="alert-error">{{ $errorMsg }}</div>
                @endif

                <div>
                    <label class="form-label">Product *</label>
                    <select wire:model="productId" class="form-input">
                        <option value="">Select a product...</option>
                        @foreach ($products as $p)
                            <option value="{{ $p->id }}">{{ $p->name }} — {{ $p->stock_qty }} in stock</option>
                        @endforeach
                    </select>
                    @error('productId')<span class="form-error">{{ $message }}</span>@enderror
                </div>

                <div style="display:grid; grid-template-columns:1fr 1fr; gap:0.75rem;">
                    <div>
                        <label class="form-label">Qty Received *</label>
                        <input wire:model="qty" type="number" min="1" class="form-input">
                        @error('qty')<span class="form-error">{{ $message }}</span>@enderror
                    </div>
                    <div>
                        <label class="form-label">Date Received *</label>
                        <input wire:model="receivedAt" type="date" class="form-input">
                    </div>
                </div>
                <div>
                    <label class="form-label">Supplier</label>
                    <input wire:model="supplier" type="text" placeholder="Optional" class="form-input">
                </div>
                <div>
                    <label class="form-label">Unit Cost</label>
                    <input wire:model="unitCost" type="number" step="0.01" min="0" placeholder="Optional" class="form-input">
                </div>
                <div>
                    <label class="form-label">Remarks</label>
                    <textarea wire:model="remarks" rows="2" class="form-input" style="resize:vertical;" placeholder="Optional notes"></textarea>
                </div>
                <button wire:click="processStockIn" class="btn-primary" style="margin-top:0.25rem;">
                    ✓ Record Stock In
                </button>
            </div>
        </div>
        @endif

        @if ($showOutForm)
        {{-- Manual Stock Out Form --}}
        <div class="card" style="border-color:rgba(192,57,43,0.15);">
            <div class="card-header" style="background:#fff8f8;">
                <span class="card-title" style="color:#8a2020;">↓ Manual Stock Out</span>
            </div>
            <div style="padding:1.5rem; display:flex; flex-direction:column; gap:1rem;">
                <div style="background:#fdf8f8; border:1px solid rgba(192,57,43,0.1); border-radius:8px; padding:0.75rem 1rem; font-size:0.78rem; color:#8a5252; line-height:1.6;">
                    ⚠ Use only for wastage or inventory corrections. This cannot be undone.
                </div>
                @if ($outErrorMsg)
                    <div class="alert-error">{{ $outErrorMsg }}</div>
                @endif
                <div>
                    <label class="form-label">Product *</label>
                    <select wire:model="outProductId" class="form-input">
                        <option value="">Select a product...</option>
                        @foreach ($products as $p)
                            <option value="{{ $p->id }}">{{ $p->name }} — {{ $p->stock_qty }} in stock</option>
                        @endforeach
                    </select>
                </div>
                <div style="display:grid; grid-template-columns:1fr 1fr; gap:0.75rem;">
                    <div>
                        <label class="form-label">Quantity *</label>
                        <input wire:model="outQty" type="number" min="1" class="form-input">
                    </div>
                    <div>
                        <label class="form-label">Reason *</label>
                        <select wire:model="outReason" class="form-input">
                            <option value="wastage">Wastage</option>
                            <option value="adjustment">Adjustment</option>
                        </select>
                    </div>
                </div>
                <div>
                    <label class="form-label">Notes *</label>
                    <textarea wire:model="outNotes" rows="2" class="form-input" style="resize:vertical;" placeholder="Reason for this adjustment..."></textarea>
                </div>
                <button wire:click="processStockOut" class="btn-danger" style="padding:0.7rem; font-size:0.82rem; font-weight:700; letter-spacing:0.06em; text-transform:uppercase; border-radius:8px; transition:all 0.15s;">
                    Record Stock Out
                </button>
            </div>
        </div>
        @endif

        {{-- Recent Movements --}}
        <div class="card">
            <div class="card-header">
                <span class="card-title">Recent Movements</span>
                <a href="{{ route('inventory.movements') }}" style="font-size:0.72rem; color:var(--caramel); font-weight:700;">View all →</a>
            </div>
            @foreach ($recentMovements as $m)
            <div style="display:flex; align-items:center; justify-content:space-between; padding:0.75rem 1.25rem; border-bottom:1px solid rgba(74,37,24,0.05);">
                <div>
                    <div style="font-size:0.82rem; font-weight:700; color:#3d2415;">{{ $m->product->name }}</div>
                    <div style="font-size:0.68rem; color:#9a7a68; text-transform:uppercase; letter-spacing:0.05em;">
                        {{ $m->reason_label }} · {{ $m->createdBy->name }}
                    </div>
                </div>
                <div style="text-align:right;">
                    <span class="badge {{ $m->type === 'in' ? 'badge-in' : 'badge-out' }}" style="font-size:0.78rem; padding:0.2rem 0.6rem;">
                        {{ $m->type === 'in' ? '+' : '−' }}{{ $m->qty }}
                    </span>
                    <div style="font-size:0.65rem; color:#c8b0a0; margin-top:0.2rem;">{{ $m->created_at->diffForHumans() }}</div>
                </div>
            </div>
            @endforeach
        </div>
    </div>
</div>
