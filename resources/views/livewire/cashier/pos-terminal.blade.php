<div>
    <div style="display:flex; height:calc(100vh - 53px); overflow:hidden;">

        {{-- LEFT PANEL --}}
        <div style="flex:1; display:flex; flex-direction:column; overflow:hidden; min-width:0;">

            <div style="padding:0.85rem 1.25rem; display:flex; align-items:center; gap:0.75rem; flex-wrap:wrap;
                        background:rgba(255,255,255,0.03); border-bottom:1px solid rgba(200,129,58,0.12);"
                 class="no-print">
                <button wire:click="$set('activeCategory','coffee')"
                    class="cat-tab {{ $activeCategory === 'coffee' ? 'active' : '' }}">
                    ☕ Coffee
                </button>
                <button wire:click="$set('activeCategory','iced_coffee')"
                    class="cat-tab {{ $activeCategory === 'iced_coffee' ? 'active' : '' }}">
                    ❄ Iced
                </button>
                <button wire:click="$set('activeCategory','non_coffee')"
                    class="cat-tab {{ $activeCategory === 'non_coffee' ? 'active' : '' }}">
                    ✦ Others
                </button>

                <div style="margin-left:auto; position:relative;">
                    <svg style="position:absolute; left:0.65rem; top:50%; transform:translateY(-50%); width:14px; height:14px; color:rgba(200,129,58,0.4);" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                    </svg>
                    <input wire:model.live.debounce.300ms="search"
                        type="text" placeholder="Search menu..."
                        class="search-input">
                </div>
            </div>

            @if ($errorMessage)
            <div class="error-bar no-print">
                <span>⚠ {{ $errorMessage }}</span>
                <button wire:click="$set('errorMessage', null)"
                    style="background:none; border:none; cursor:pointer; color:rgba(200,129,58,0.5); font-size:1rem; padding:0 0.25rem;">✕</button>
            </div>
            @endif

            <div style="flex:1; overflow-y:auto; padding:1.1rem 1.25rem;">
                @if ($this->products->isEmpty())
                    <div style="display:flex; flex-direction:column; align-items:center; justify-content:center; height:100%; color:rgba(245,234,216,0.2); gap:0.75rem;">
                        <svg style="width:48px; height:48px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        <span style="font-size:0.85rem; font-family:'Playfair Display',serif;">No items on the menu</span>
                    </div>
                @else
                <div style="display:grid; grid-template-columns:repeat(auto-fill, minmax(210px, 1fr)); gap:1rem;">
                    @foreach ($this->products as $product)
                    <button wire:click="addToCart({{ $product->id }})"
                        wire:key="prod-{{ $product->id }}"
                        class="product-card {{ $product->stock_qty < 1 ? 'out-of-stock' : '' }}"
                        {{ $product->stock_qty < 1 ? 'disabled' : '' }}>

                        <div style="font-size:1.6rem; margin-bottom:0.5rem; line-height:1;">
                            @if($product->category === 'coffee') ☕
                            @elseif($product->category === 'iced_coffee') 🧊
                            @else 🥤
                            @endif
                        </div>

                        <div class="product-name">{{ $product->name }}</div>
                        <div class="product-price">₱{{ number_format($product->selling_price, 2) }}</div>

                        <div class="product-stock">
                            @if ($product->stock_qty < 1)
                                <span class="stock-out">Out of stock</span>
                            @elseif ($product->isLowStock())
                                <span class="stock-low">⚑ {{ $product->stock_qty }} left</span>
                            @else
                                {{ $product->stock_qty }} in stock
                            @endif
                        </div>
                    </button>
                    @endforeach
                </div>
                @endif
            </div>

            <div style="padding:0.5rem 1.25rem; border-top:1px solid rgba(200,129,58,0.08); display:flex; align-items:center; gap:1rem; background:rgba(0,0,0,0.15);" class="no-print">
                <span style="font-size:0.65rem; letter-spacing:0.08em; text-transform:uppercase; color:rgba(245,234,216,0.25);">
                    Cashier: {{ auth()->user()->name }}
                </span>
                <span style="font-size:0.65rem; color:rgba(200,129,58,0.3);">•</span>
                <span style="font-size:0.65rem; letter-spacing:0.06em; color:rgba(245,234,216,0.2); font-family:'Playfair Display',serif;"
                      x-data x-text="new Date().toLocaleString('en-PH', {dateStyle:'medium', timeStyle:'short'})"></span>
            </div>
        </div>

        {{-- RIGHT PANEL --}}
        <div class="cart-panel no-print" style="width:360px; display:flex; flex-direction:column; flex-shrink:0;">

            <div class="cart-header" style="display:flex; align-items:center; justify-content:space-between;">
                <div style="display:flex; align-items:center; gap:0.65rem;">
                    <span class="cart-title">Order</span>
                    @if ($this->cartCount > 0)
                        <span style="background:var(--caramel); color:var(--espresso); font-size:0.65rem; font-weight:700; padding:0.15rem 0.5rem; border-radius:10px; letter-spacing:0.04em;">
                            {{ $this->cartCount }}
                        </span>
                    @endif
                </div>
                @if (!empty($cart))
                    <button wire:click="clearCart"
                        style="font-size:0.65rem; letter-spacing:0.08em; text-transform:uppercase; color:rgba(245,234,216,0.25); background:none; border:none; cursor:pointer; transition:color 0.15s;"
                        onmouseover="this.style.color='rgba(200,129,58,0.6)'" onmouseout="this.style.color='rgba(245,234,216,0.25)'">
                        Clear
                    </button>
                @endif
            </div>

            <div style="flex:1; overflow-y:auto; padding:0.5rem 0.75rem;">
                @forelse ($cart as $key => $item)
                    <div wire:key="cart-{{ $key }}"
                        style="display:flex; align-items:center; gap:0.6rem; padding:0.7rem 0.5rem; border-bottom:1px solid rgba(200,129,58,0.1);">
                        <div style="flex:1; min-width:0;">
                            <div class="cart-item-name">{{ $item['name'] }}</div>
                            <div class="cart-item-price">₱{{ number_format($item['price'], 2) }} each</div>
                        </div>
                        <div style="display:flex; align-items:center; gap:0.35rem;">
                            <button wire:click="decrementQty('{{ $key }}')" class="qty-btn">−</button>
                            <span style="width:22px; text-align:center; font-weight:700; font-size:0.85rem; color:var(--cream);">{{ $item['qty'] }}</span>
                            <button wire:click="incrementQty('{{ $key }}')" class="qty-btn">+</button>
                        </div>
                        <div style="width:60px; text-align:right; font-weight:700; font-size:0.82rem; color:var(--caramel);">
                            ₱{{ number_format($item['price'] * $item['qty'], 2) }}
                        </div>
                    </div>
                @empty
                    <div style="display:flex; flex-direction:column; align-items:center; justify-content:center; height:180px; color:rgba(245,234,216,0.15); gap:0.6rem;">
                        <span style="font-size:2rem;">☕</span>
                        <span style="font-size:0.78rem; font-family:'Playfair Display',serif;">Order is empty</span>
                        <span style="font-size:0.65rem; letter-spacing:0.06em; text-transform:uppercase;">Tap a menu item to add</span>
                    </div>
                @endforelse
            </div>

            <div style="border-top:1px solid rgba(200,129,58,0.15); padding:1rem;">

                <div style="display:flex; justify-content:space-between; font-size:0.78rem; color:rgba(245,234,216,0.45); margin-bottom:0.4rem;">
                    <span>Subtotal</span>
                    <span>₱{{ number_format($this->subtotal, 2) }}</span>
                </div>

                <hr class="cart-divider" style="margin-bottom:0.6rem;">

                <div style="display:flex; justify-content:space-between; align-items:baseline; margin-bottom:0.9rem;">
                    <span style="font-family:'Playfair Display',serif; font-size:0.95rem; color:var(--latte);">Total</span>
                    <span style="font-family:'Playfair Display',serif; font-size:1.5rem; font-weight:700; color:var(--caramel);">
                        ₱{{ number_format($this->total, 2) }}
                    </span>
                </div>

                {{-- Cash input --}}
                <div style="margin-bottom:0.6rem;">
                    <label style="font-size:0.62rem; letter-spacing:0.1em; text-transform:uppercase; color:rgba(245,234,216,0.35); display:block; margin-bottom:0.4rem;">
                        Cash Tendered (₱)
                    </label>

                    <!-- ✅ Keyed input so it resets after checkoutToken changes -->
                    <input
                        wire:key="cashReceived-{{ $checkoutToken }}"
                        wire:model.live="cashReceived"
                        type="number"
                        min="0"
                        step="0.01"
                        placeholder="0.00"
                        class="cash-input">
                </div>

                {{-- Change --}}
                @if ($this->cashFloat > 0 && $this->cashFloat >= $this->total)
                <div style="display:flex; justify-content:space-between; background:rgba(45,106,45,0.12); border:1px solid rgba(78,180,78,0.15); border-radius:8px; padding:0.55rem 0.8rem; margin-bottom:0.75rem;">
                    <span style="font-size:0.75rem; color:rgba(150,220,150,0.7);">Change</span>
                    <span style="font-size:0.9rem; font-weight:700; color:#6ecf6e;">₱{{ number_format($this->change, 2) }}</span>
                </div>
                @endif

                <input type="hidden" wire:model="checkoutToken">

                {{-- Confirm --}}
                <button wire:click="confirmCheckout"
                    wire:loading.attr="disabled"
                    wire:target="confirmCheckout"
                    class="btn-confirm"
                    @if (empty($cart) || $this->cashFloat < $this->total || $this->cashFloat <= 0) disabled @endif>
                    <span wire:loading.remove wire:target="confirmCheckout">Confirm Order</span>
                    <span wire:loading wire:target="confirmCheckout">Processing…</span>
                </button>
            </div>
        </div>
    </div>

    {{-- RECEIPT MODAL --}}
    @if ($showReceipt && $receiptData)
    <div class="receipt-modal-bg" style="position:fixed; inset:0; display:flex; align-items:center; justify-content:center; z-index:500; padding:1rem;">
        <div class="receipt-card">
            <div class="receipt-header">
                <span class="receipt-logo">✦ Lucky Star Coffee</span>
                <span class="receipt-sub">Official Receipt</span>
            </div>

            <div class="receipt-body">
                <div class="receipt-row">
                    <span class="receipt-label">Receipt #</span>
                    <span style="font-weight:700;">{{ $receiptData['receipt_no'] }}</span>
                </div>
                <div class="receipt-row">
                    <span class="receipt-label">Date</span>
                    <span>{{ $receiptData['created_at'] }}</span>
                </div>
                <div class="receipt-row">
                    <span class="receipt-label">Cashier</span>
                    <span>{{ $receiptData['cashier'] }}</span>
                </div>

                <hr class="receipt-dashed">

                @foreach ($receiptData['items'] as $item)
                <div class="receipt-row">
                    <span style="flex:1; padding-right:0.5rem;">{{ $item['name'] }} × {{ $item['qty'] }}</span>
                    <span>₱{{ number_format($item['line_total'], 2) }}</span>
                </div>
                @endforeach

                <hr class="receipt-dashed">

                <div class="receipt-row receipt-total">
                    <span>TOTAL</span>
                    <span>₱{{ number_format($receiptData['total'], 2) }}</span>
                </div>
                <div class="receipt-row" style="margin-top:0.3rem;">
                    <span class="receipt-label">Cash</span>
                    <span>₱{{ number_format($receiptData['cash_received'], 2) }}</span>
                </div>
                <div class="receipt-row receipt-change">
                    <span>Change</span>
                    <span>₱{{ number_format($receiptData['change_amount'], 2) }}</span>
                </div>

                <hr class="receipt-dashed">
                <div style="text-align:center; font-size:0.7rem; color:#9a7a68; font-style:italic;">
                    Thank you for choosing Lucky Star!<br>
                    <span style="font-size:0.62rem; letter-spacing:0.08em;">· · · · · · · · · ·</span>
                </div>
            </div>

            <div style="padding:0.9rem 1.25rem; background:#f7f0e6; border-top:1px dashed #d4b896; display:flex; gap:0.65rem;">
                <button onclick="window.print()" class="btn-print">🖨 Print</button>
                <button wire:click="closeReceipt" class="btn-new-sale">New Order →</button>
            </div>
        </div>
    </div>
    @endif

    <!-- ✅ LUCKY STAR TOAST -->
    <div
        id="sale-toast"
        style="
            position: fixed;
            top: 18px;
            right: 18px;
            z-index: 99999;
            display: none;
            align-items: center;
            gap: 10px;
            padding: 12px 14px;
            border-radius: 16px;

            background: linear-gradient(135deg, rgba(200,129,58,.95), rgba(212,168,71,.92));
            color: rgba(26,15,10,0.95);

            font-weight: 900;
            letter-spacing: .05em;
            box-shadow: 0 18px 45px rgba(0,0,0,.35);
            border: 1px solid rgba(255,255,255,.22);
            backdrop-filter: blur(6px);
        "
    >
        <span style="font-size: 1.05rem;">✦</span>
        <span style="font-family:'Playfair Display',serif;">Payment Successful</span>
        <span style="margin-left:6px; opacity:.85;">☕</span>
    </div>

    <script>
        (function () {
            function showSaleToast() {
                const toast = document.getElementById('sale-toast');
                if (!toast) return;

                toast.style.display = 'flex';
                toast.style.opacity = '1';
                toast.style.transform = 'translateY(0)';

                clearTimeout(window.__saleToastT1);
                clearTimeout(window.__saleToastT2);

                window.__saleToastT1 = setTimeout(() => {
                    toast.style.opacity = '0';
                    toast.style.transform = 'translateY(-6px)';
                    window.__saleToastT2 = setTimeout(() => {
                        toast.style.display = 'none';
                    }, 250);
                }, 1400);
            }

            window.addEventListener('saleCompleted', showSaleToast);
        })();
    </script>
</div>
