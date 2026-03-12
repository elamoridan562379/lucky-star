<div @if(auth()->user()->isCashier()) style="
    min-height: calc(100vh - 53px);
    padding: 2.2rem 1.6rem;

    /* darker coffee background (same vibe sa POS) */
    background:
        radial-gradient(circle at 20% 20%, rgba(200,129,58,.18), transparent 45%),
        radial-gradient(circle at 80% 35%, rgba(200,129,58,.12), transparent 50%),
        linear-gradient(180deg, #2b170f 0%, #1a0f0a 100%);
" @endif>

    <div style="max-width:1100px; margin:0 auto;">

        <div style="display:flex; align-items:flex-start; justify-content:space-between; gap:1rem; flex-wrap:wrap; margin-bottom:1.25rem;">
            <div style="display:flex; align-items:flex-start; gap:.9rem;">

                <div>
                    <h1 class="page-title" style="
                        margin:0;
                        color: rgba(245,234,216,.92);
                        text-shadow: 0 10px 25px rgba(0,0,0,.35);
                    ">
                        Transaction History
                    </h1>
                    <div style="margin-top:.35rem; font-size:.92rem; color:rgba(245,234,216,.62);">
                        View receipts and totals by date range.
                    </div>
                </div>
            </div>
        </div>

        {{-- Filters --}}
        <div style="
            background: rgba(255,255,255,0.08);
            border: 1px solid rgba(200,129,58,0.18);
            border-radius: 16px;
            padding: 1rem 1.25rem;
            margin-bottom: 1.1rem;
            display: flex;
            flex-wrap: wrap;
            gap: 0.85rem;
            align-items: flex-end;
            box-shadow: 0 12px 34px rgba(0,0,0,.28);
            backdrop-filter: blur(6px);
        ">
            <div>
                <label style="display:block; font-size:0.7rem; font-weight:900; letter-spacing:0.1em; text-transform:uppercase; color:rgba(245,234,216,.75); margin-bottom:0.35rem;">
                    From
                </label>
                <input wire:model.live="dateFrom" type="date"
                    style="
                        width:160px;
                        border:1px solid rgba(200,129,58,0.25);
                        border-radius:10px;
                        padding:.55rem .75rem;
                        font-size:.9rem;
                        color:rgba(245,234,216,.92);
                        background:rgba(0,0,0,.28);
                        outline:none;
                    ">
            </div>

            <div>
                <label style="display:block; font-size:0.7rem; font-weight:900; letter-spacing:0.1em; text-transform:uppercase; color:rgba(245,234,216,.75); margin-bottom:0.35rem;">
                    To
                </label>
                <input wire:model.live="dateTo" type="date"
                    style="
                        width:160px;
                        border:1px solid rgba(200,129,58,0.25);
                        border-radius:10px;
                        padding:.55rem .75rem;
                        font-size:.9rem;
                        color:rgba(245,234,216,.92);
                        background:rgba(0,0,0,.28);
                        outline:none;
                    ">
            </div>

            <div>
                <label style="display:block; font-size:0.7rem; font-weight:900; letter-spacing:0.1em; text-transform:uppercase; color:rgba(245,234,216,.75); margin-bottom:0.35rem;">
                    Search
                </label>
                <input wire:model.live.debounce.300ms="search" type="text" placeholder="Receipt number…"
                    style="
                        width:220px;
                        border:1px solid rgba(200,129,58,0.25);
                        border-radius:10px;
                        padding:.55rem .75rem;
                        font-size:.9rem;
                        color:rgba(245,234,216,.92);
                        background:rgba(0,0,0,.28);
                        outline:none;
                    ">
            </div>
        </div>

        {{-- Table --}}
        <div style="
            background: rgba(255,255,255,0.96);
            border:1px solid rgba(74,37,24,0.14);
            border-radius:18px;
            overflow:hidden;
            box-shadow:0 18px 40px rgba(0,0,0,.35);
        ">
            <table style="width:100%; border-collapse:collapse; font-size:0.9rem;">
                <thead>
                    <tr style="background:#faf5ee; border-bottom:1px solid rgba(74,37,24,0.12);">
                        <th style="padding:.85rem 1rem; text-align:left; font-size:.7rem; letter-spacing:.12em; text-transform:uppercase; color:#9a7a68; font-weight:900;">Receipt #</th>
                        <th style="padding:.85rem 1rem; text-align:left; font-size:.7rem; letter-spacing:.12em; text-transform:uppercase; color:#9a7a68; font-weight:900;">Date & Time</th>
                        <th style="padding:.85rem 1rem; text-align:left; font-size:.7rem; letter-spacing:.12em; text-transform:uppercase; color:#9a7a68; font-weight:900;">Cashier</th>
                        <th style="padding:.85rem 1rem; text-align:right; font-size:.7rem; letter-spacing:.12em; text-transform:uppercase; color:#9a7a68; font-weight:900;">Total</th>
                        <th style="padding:.85rem 1rem; text-align:center; font-size:.7rem; letter-spacing:.12em; text-transform:uppercase; color:#9a7a68; font-weight:900;">Receipt</th>
                    </tr>
                </thead>

                <tbody>
                    @forelse ($transactions as $txn)
                        <tr style="border-bottom:1px solid rgba(74,37,24,0.06);"
                            onmouseover="this.style.background='#fdf8f2'"
                            onmouseout="this.style.background='transparent'">
                            <td style="padding:.9rem 1rem; font-family:'Courier New',monospace; font-weight:900; color:#c8813a;">
                                {{ $txn->receipt_no }}
                            </td>
                            <td style="padding:.9rem 1rem; color:#4a2518;">
                                {{ $txn->created_at->format('M d, Y  H:i') }}
                            </td>
                            <td style="padding:.9rem 1rem; color:#4a2518;">
                                {{ $txn->cashier->name }}
                            </td>
                            <td style="padding:.9rem 1rem; text-align:right; font-weight:900; font-family:'Playfair Display',serif; color:#2d1810;">
                                ₱{{ number_format($txn->total, 2) }}
                            </td>
                            <td style="padding:.9rem 1rem; text-align:center;">
                                <a href="{{ route('transactions.show', $txn->id) }}"
                                   style="
                                        display:inline-flex; align-items:center; justify-content:center;
                                        padding:.42rem .85rem;
                                        border-radius:12px;
                                        border:1px solid rgba(200,129,58,.25);
                                        background: rgba(200,129,58,.12);
                                        color:#4a2518;
                                        font-weight:900;
                                        letter-spacing:.08em;
                                        text-transform:uppercase;
                                        font-size:.75rem;
                                        text-decoration:none;
                                   ">
                                    View →
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" style="padding:3rem; text-align:center; color:#9a7a68; font-family:'Playfair Display',serif; font-style:italic;">
                                No transactions for this period.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>

            <div style="padding:1rem 1.25rem; border-top:1px solid rgba(74,37,24,0.08); background:#fff;">
                {{ $transactions->links() }}
            </div>
        </div>

    </div>
</div>
