<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AuthenticatedSessionController extends Controller
{
    /**
     * Display the login view.
     */
    public function create(): View
    {
        return view('auth.login');
    }

    /**
     * Handle an incoming authentication request.
     */
    public function store(LoginRequest $request): RedirectResponse
    {
        $request->authenticate();

        $request->session()->regenerate();

        $user = $request->user();
        $role = $user?->role ?? 'cashier';

        // Manager/Admin -> Manager dashboard
        if (in_array($role, ['manager', 'admin'], true)) {
            return redirect()->route('dashboard'); // /manager/dashboard
        }

        // Inventory Clerk -> Inventory dashboard
        if ($role === 'inventory_clerk') {
            return redirect()->route('inventory.dashboard'); // /inventory/dashboard
        }

        // Cashier -> POS
        return redirect()->route('pos'); // /pos
    }

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/');
    }
}
