<div style="
    min-height: calc(100vh - 53px);
    padding: 2rem 1.5rem;
    background: #f0e8d8;  /* ✅ same latte */
">
    <div style="max-width:860px; margin:0 auto;">

        <div style="display:flex; align-items:flex-start; justify-content:space-between; gap:1rem; flex-wrap:wrap; margin-bottom:1rem;">
            <div style="display:flex; align-items:flex-start; gap:.9rem;">
                <button type="button"
                    onclick="history.length > 1 ? history.back() : window.location.href='{{ route('transactions.index') }}'"
                    style="
                        display:inline-flex; align-items:center; gap:.55rem;
                        padding:.55rem .95rem;
                        border-radius:12px;
                        border:1px solid rgba(74,37,24,0.14);
                        background:#fff;
                        color:#4a2518;
                        font-weight:900;
                        letter-spacing:.05em;
                        cursor:pointer;
                        box-shadow:0 2px 10px rgba(45,24,16,.06);
                   ">
                    ← Back
                </button>

                <div>
                    <h1 style="font-family:'Playfair Display',serif; font-size:1.85rem; font-weight:900; color:#2d1810; margin:0;">
                        Receipt Details
                    </h1>
                    <div style="margin-top:.25rem; font-size:.9rem; color:#9a7a68;">
                        View transaction details
                    </div>
                </div>
            </div>

            <button type="button" onclick="window.print()"
                style="
                    display:inline-flex; align-items:center; gap:.55rem;
                    padding:.55rem .95rem;
                    border-radius:12px;
                    border:1px solid rgba(200,129,58,.22);
                    background: rgba(200,129,58,.12);
                    color:#4a2518;
                    font-weight:900;
                    letter-spacing:.06em;
                    cursor:pointer;
                ">
                🖨 Print
            </button>
        </div>

        <div style="
            background:#fff;
            border:1px solid rgba(74,37,24,0.12);
            border-radius:18px;
            overflow:hidden;
            box-shadow:0 10px 26px rgba(45,24,16,.10);
        ">
            <div style="padding:1.1rem 1.25rem; background:#faf5ee; border-bottom:1px solid rgba(74,37,24,0.10);">
                <div style="font-family:'Playfair Display',serif; font-weight:900; color:#c8813a; font-size:1.05rem;">
                    ✦ Lucky Star Coffee Shop
                </div>
                <div style="font-size:.7rem; letter-spacing:.12em; text-transform:uppercase; color:#9a7a68;">
                    Official Receipt
                </div>
            </div>

            <div style="padding:1.25rem;">
                <div style="display:grid; grid-template-columns: 1fr 1fr; gap:.75rem 1rem; margin-bottom:1rem;">
                    <div style="border:1px solid rgba(74,37,24,0.10); border-radius:14px; padding:.8rem 1rem;">
                        <div style="font-size:.65rem; letter-spacing:.12em; text-transform:uppercase; color:#9a7a68; font-weight:900;">Receipt #</div>
                        <div style="margin-top:.25rem; font-weight:900; color:#2d1810;">{{ $transaction->receipt_no }}</div>
                    </div>
                    <div style="border:1px solid rgba(74,37,24,0.10); border-radius:14px; padding:.8rem 1rem;">
                        <div style="font-size:.65rem; letter-spacing:.12em; text-transform:uppercase; color:#9a7a68; font-weight:900;">Date & Time</div>
                        <div style="margin-top:.25rem; font-weight:800; color:#2d1810;">{{ $transaction->created_at->format('M d, Y H:i') }}</div>
                    </div>
                    <div style="border:1px solid rgba(74,37,24,0.10); border-radius:14px; padding:.8rem 1rem;">
                        <div style="font-size:.65rem; letter-spacing:.12em; text-transform:uppercase; color:#9a7a68; font-weight:900;">Cashier</div>
                        <div style="margin-top:.25rem; font-weight:800; color:#2d1810;">{{ $transaction->cashier->name }}</div>
                    </div>
                    <div style="border:1px solid rgba(74,37,24,0.10); border-radius:14px; padding:.8rem 1rem;">
                        <div style="font-size:.65rem; letter-spacing:.12em; text-transform:uppercase; color:#9a7a68; font-weight:900;">Total</div>
                        <div style="margin-top:.15rem; font-family:'Playfair Display',serif; font-size:1.25rem; font-weight:900; color:#c8813a;">
                            ₱{{ number_format($transaction->total, 2) }}
                        </div>
                    </div>
                </div>

                <div style="height:1px; background:rgba(74,37,24,0.10); margin:1rem 0;"></div>

                <div style="font-size:.7rem; letter-spacing:.12em; text-transform:uppercase; color:#9a7a68; font-weight:900; margin-bottom:.6rem;">
                    Items
                </div>

                <div style="border:1px solid rgba(74,37,24,0.10); border-radius:16px; overflow:hidden;">
                    <div style="display:flex; justify-content:space-between; padding:.75rem 1rem; background:#faf5ee; border-bottom:1px solid rgba(74,37,24,0.08);">
                        <span style="font-size:.65rem; letter-spacing:.12em; text-transform:uppercase; color:#9a7a68; font-weight:900;">Item</span>
                        <span style="font-size:.65rem; letter-spacing:.12em; text-transform:uppercase; color:#9a7a68; font-weight:900;">Total</span>
                    </div>

                    <div style="padding:.75rem 1rem;">
                        @foreach ($transaction->items as $item)
                            <div style="display:flex; justify-content:space-between; gap:1rem; padding:.55rem 0; border-bottom:1px solid rgba(74,37,24,0.06);">
                                <div style="flex:1; min-width:0;">
                                    <div style="font-weight:900; color:#2d1810;">{{ $item->product->name }}</div>
                                    <div style="font-size:.85rem; color:#9a7a68;">
                                        {{ $item->qty }} × ₱{{ number_format($item->unit_price, 2) }}
                                    </div>
                                </div>
                                <div style="font-weight:900; color:#2d1810;">
                                    ₱{{ number_format($item->line_total, 2) }}
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>

                <div style="display:grid; grid-template-columns:1fr 1fr; gap:1rem; margin-top:1rem;">
                    <div style="border:1px solid rgba(74,37,24,0.10); border-radius:14px; padding:.8rem 1rem;">
                        <div style="font-size:.65rem; letter-spacing:.12em; text-transform:uppercase; color:#9a7a68; font-weight:900;">Cash</div>
                        <div style="margin-top:.25rem; font-weight:900; color:#2d1810;">
                            ₱{{ number_format($transaction->payment->cash_received, 2) }}
                        </div>
                    </div>
                    <div style="border:1px solid rgba(74,37,24,0.10); border-radius:14px; padding:.8rem 1rem;">
                        <div style="font-size:.65rem; letter-spacing:.12em; text-transform:uppercase; color:#9a7a68; font-weight:900;">Change</div>
                        <div style="margin-top:.25rem; font-weight:900; color:#2d1810;">
                            ₱{{ number_format($transaction->payment->change_amount, 2) }}
                        </div>
                    </div>
                </div>

                <div style="margin-top:1rem; text-align:center; font-size:.85rem; color:#9a7a68; font-style:italic;">
                    Thank you for choosing Lucky Star! ☕
                </div>
            </div>
        </div>
    </div>
</div>
