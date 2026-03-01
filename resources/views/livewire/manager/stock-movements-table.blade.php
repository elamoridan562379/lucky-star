<div>
    <h1 class="page-title">Movement Log</h1>

    {{-- Filters --}}
    <div class="card" style="padding:1rem 1.25rem; margin-bottom:1.25rem; display:flex; flex-wrap:wrap; gap:0.75rem; align-items:flex-end;">
        <div>
            <label class="form-label">From</label>
            <input wire:model.defer="dateFrom" type="date" class="form-input" style="width:150px;">
        </div>

        <div>
            <label class="form-label">To</label>
            <input wire:model.defer="dateTo" type="date" class="form-input" style="width:150px;">
        </div>

        <div>
            <label class="form-label">Type</label>
            <select wire:model.defer="type" class="form-input" style="width:130px;">
                <option value="">All Types</option>
                <option value="in">Stock In</option>
                <option value="out">Stock Out</option>
            </select>
        </div>

        <div>
            <label class="form-label">Reason</label>
            <select wire:model.defer="reason" class="form-input" style="width:140px;">
                <option value="">All Reasons</option>
                <option value="sale">Sale</option>
                <option value="restock">Restock</option>
                <option value="adjustment">Adjustment</option>
                <option value="wastage">Wastage</option>
                <option value="void">Void</option>
            </select>
        </div>

        <div>
            <label class="form-label">Product</label>
            <select wire:model.defer="productId" class="form-input" style="width:180px;">
                <option value="">All Products</option>
                @foreach ($products as $p)
                    <option wire:key="p-{{ $p->id }}" value="{{ $p->id }}">{{ $p->name }}</option>
                @endforeach
            </select>
        </div>

        {{-- ✅ Clear --}}
        <button
    type="button"
    wire:click="clearFilters"
    wire:loading.attr="disabled"
    wire:target="clearFilters"
    class="btn-secondary"
    style="align-self:flex-end; min-width:110px;"
>
    <span wire:loading.remove wire:target="clearFilters">Clear</span>
    <span wire:loading wire:target="clearFilters">Clearing...</span>
</button>
    </div>

    <div class="card">
        <table class="data-table">
            <thead>
                <tr>
                    <th>Date & Time</th>
                    <th>Product</th>
                    <th style="text-align:center;">Type</th>
                    <th style="text-align:right;">Qty</th>
                    <th>Reason</th>
                    <th>Reference</th>
                    <th>By</th>
                </tr>
            </thead>

            <tbody>
                @forelse ($movements as $m)
                    <tr wire:key="mv-{{ $m->id }}">
                        <td style="font-size:0.75rem; color:#7a5c44; white-space:nowrap;">
                            {{ $m->created_at->format('M d, Y H:i') }}
                        </td>

                        <td style="font-weight:700; font-family:'Playfair Display',serif; font-size:0.88rem;">
                            {{ $m->product->name }}
                        </td>

                        <td style="text-align:center;">
                            <span class="badge {{ $m->type === 'in' ? 'badge-in' : 'badge-out' }}">
                                {{ $m->type === 'in' ? '↑ In' : '↓ Out' }}
                            </span>
                        </td>

                        <td style="text-align:right; font-weight:700; font-size:0.9rem; color:{{ $m->type === 'in' ? '#2d6a2d' : '#8a2020' }};">
                            {{ $m->type === 'in' ? '+' : '−' }}{{ $m->qty }}
                        </td>

                        <td style="color:#6a4a35; font-size:0.8rem; text-transform:capitalize;">
                            {{ $m->reason_label }}
                        </td>

                        <td style="font-size:0.75rem; color:#9a7a68;">
                            @if ($m->ref_link)
                                <a href="{{ $m->ref_link }}" style="color:var(--caramel); font-weight:700;">
                                    {{ $m->ref_type }} #{{ $m->ref_id }}
                                </a>
                            @elseif ($m->notes)
                                <span style="font-style:italic;">{{ Str::limit($m->notes, 35) }}</span>
                            @else
                                <span style="color:#d4c4b4;">—</span>
                            @endif
                        </td>

                        <td style="font-size:0.75rem; color:#9a7a68;">
                            {{ $m->createdBy->name }}
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" style="text-align:center; padding:3rem; color:#c8b0a0; font-family:'Playfair Display',serif; font-style:italic;">
                            No movements found for the selected filters.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        <div style="padding:1rem 1.25rem; border-top:1px solid rgba(74,37,24,0.06);">
            {{ $movements->links() }}
        </div>
    </div>
</div>
