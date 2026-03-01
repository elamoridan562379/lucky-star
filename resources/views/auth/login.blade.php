<x-guest-layout>
    <x-auth-session-status class="mb-4" :status="session('status')" />

    <form method="POST" action="{{ route('login') }}">
        @csrf

        <div style="margin-top:6px;">
            <label style="display:block; font-weight:800; font-size:14px; margin-bottom:8px; color:#3a1f14;">
                Email or Username
            </label>
            <input
                id="email"
                type="email"
                name="email"
                value="{{ old('email') }}"
                required
                autofocus
                placeholder="admin@luckystar.com"
                style="
                    width:100%;
                    padding:14px 16px;
                    border-radius:12px;
                    border:2px solid rgba(200,129,58,.45);
                    outline:none;
                    font-size:15px;
                "
            />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <div style="margin-top:18px;">
            <label style="display:block; font-weight:800; font-size:14px; margin-bottom:8px; color:#3a1f14;">
                Password
            </label>
            <input
                id="password"
                type="password"
                name="password"
                required
                placeholder="Enter your password"
                style="
                    width:100%;
                    padding:14px 16px;
                    border-radius:12px;
                    border:1px solid rgba(74,37,24,.18);
                    outline:none;
                    font-size:15px;
                "
            />
            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <div style="margin-top:16px; display:flex; align-items:center; justify-content:space-between;">
            <label style="display:flex; align-items:center; gap:10px; color:rgba(74,37,24,.75); font-size:14px;">
                <input type="checkbox" name="remember" style="accent-color:#c8813a;">
                Remember Me
            </label>

            @if (Route::has('password.request'))
                <a class="link" href="{{ route('password.request') }}">Forgot Password?</a>
            @endif
        </div>

        <button type="submit"
            style="
                margin-top:18px;
                width:100%;
                padding:14px 16px;
                border:none;
                border-radius:14px;
                background: linear-gradient(135deg, #a9581d 0%, #c8813a 60%, #d4a847 100%);
                color:#fff;
                font-weight:800;
                font-size:16px;
                cursor:pointer;
                box-shadow:0 14px 30px rgba(200,129,58,.35);
            ">
            Sign In
        </button>

        @if (Route::has('register'))
            <div style="text-align:center; margin-top:14px; color:rgba(74,37,24,.75); font-size:14px;">
                Don't have an account?
                <a class="link" href="{{ route('register') }}">Sign Up</a>
            </div>
        @endif
    </form>
</x-guest-layout>
