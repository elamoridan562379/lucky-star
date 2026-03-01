# ☕ Lucky Star Coffee Shop — POS + Inventory System

## Tech Stack
- **Laravel** (latest stable) + PHP 8.2+
- **Livewire v3** + Alpine.js
- **TailwindCSS** (via Vite)
- **MySQL** 8+
- **Laravel Breeze** (session auth)

---

## 🚀 Installation Steps

### 1. Create Laravel project
```bash
composer create-project laravel/laravel lucky-star
cd lucky-star
```

### 2. Install required packages
```bash
# Auth scaffolding
composer require laravel/breeze --dev
php artisan breeze:install blade

# Livewire v3
composer require livewire/livewire

# TailwindCSS is included via Breeze
npm install && npm run build
```

### 3. Configure .env
```env
APP_NAME="Lucky Star Coffee"
APP_URL=http://localhost

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=lucky_star
DB_USERNAME=root
DB_PASSWORD=your_password
```

### 4. Copy project files
Copy all files from this delivery into your Laravel project, maintaining the same directory structure.

### 5. Register middleware

**Laravel 11+ (`bootstrap/app.php`):**
```php
->withMiddleware(function (Middleware $middleware) {
    $middleware->alias([
        'role' => \App\Http\Middleware\RoleMiddleware::class,
    ]);
})
```

**Laravel 10 (`app/Http/Kernel.php`):**
```php
protected $routeMiddleware = [
    // ...existing...
    'role' => \App\Http\Middleware\RoleMiddleware::class,
];
```

### 6. Run migrations + seed
```bash
php artisan migrate
php artisan db:seed
```

### 7. Build assets + serve
```bash
npm run build
php artisan serve
```

---

## 🔑 Default Credentials

| Role    | Email                      | Password    |
|---------|----------------------------|-------------|
| Manager | manager@luckystar.com      | manager123  |
| Cashier | cashier@luckystar.com      | cashier123  |

---

## 📁 File Structure
```
app/
├── Http/
│   ├── Middleware/
│   │   └── RoleMiddleware.php          ← RBAC enforcement
│   └── Livewire/
│       ├── Cashier/
│       │   ├── PosTerminal.php         ← Main POS
│       │   ├── TransactionHistory.php
│       │   └── ReceiptView.php
│       └── Manager/
│           ├── DashboardOverview.php
│           ├── ProductManager.php      ← CRUD w/ modal
│           ├── InventoryStockIn.php    ← Stock In + manual Out
│           ├── StockMovementsTable.php
│           ├── SalesReport.php
│           └── UserManager.php
├── Models/
│   ├── User.php
│   ├── Product.php
│   ├── Transaction.php
│   ├── TransactionItem.php
│   ├── Payment.php
│   ├── StockMovement.php
│   └── InventoryReceipt.php
├── Services/
│   ├── SaleService.php                 ← Atomic checkout
│   └── InventoryService.php            ← Stock in/out
└── Providers/
    └── AppServiceProvider.php          ← Livewire registration

database/
├── migrations/                         ← All 7 tables
└── seeders/
    └── DatabaseSeeder.php              ← 2 users + 18 products

resources/views/
├── layouts/
│   ├── cashier.blade.php
│   ├── manager.blade.php
│   └── print.blade.php
└── livewire/
    ├── cashier/
    │   ├── pos-terminal.blade.php
    │   ├── transaction-history.blade.php
    │   └── receipt-view.blade.php
    └── manager/
        ├── dashboard-overview.blade.php
        ├── product-manager.blade.php
        ├── inventory-stock-in.blade.php
        ├── stock-movements-table.blade.php
        ├── sales-report.blade.php
        └── user-manager.blade.php

routes/
└── web.php

tests/Feature/
└── PosSystemTest.php                   ← 8 test cases
```

---

## 🧪 Running Tests
```bash
php artisan test --filter PosSystemTest
```

**Test Coverage:**
1. ✅ Stock-in increases qty and logs movement
2. ✅ Sale creates stock-out movement and decrements stock
3. ✅ Checkout blocked on insufficient stock
4. ✅ Duplicate token returns same transaction (idempotency)
5. ✅ Manual stock-out blocked if insufficient
6. ✅ Atomic rollback on multi-item failure
7. ✅ Inactive product blocked at checkout
8. ✅ RBAC — cashier cannot access manager routes

---

## 🔒 Security Features
- **Row locking** (`lockForUpdate()`) prevents overselling under concurrent load
- **Idempotency token** prevents double-charge on network retry
- **Server-side RBAC** — middleware blocks all unauthorized routes
- **Input validation** on all Livewire forms
- **Atomic transactions** — no partial state possible

---

## 💡 Key Architecture Notes

### Stock Dual-Source Safety
`products.stock_qty` is updated by services, never directly. Use `InventoryService::reconcile($productId)` to audit drift.

### Receipt Number Generation
`LS-YYYYMMDD-XXXX` — daily sequence with `lockForUpdate()` to prevent duplicates.

### Extension Points
- Add credit card payment: extend `payments.method` ENUM + update `SaleService`
- Add discounts: implement in `SaleService::checkout()` before total calculation
- Add void flow: add `voided_at` to transactions + reverse stock movement
