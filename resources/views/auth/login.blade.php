<x-guest-layout>
    <x-auth-session-status class="mb-4" :status="session('status')" />

    <form method="POST" action="{{ route('login') }}" id="loginForm">
        @csrf



        <div class="form-group">
            <label for="email" class="form-label">
                Email or Username
            </label>
            <input
                id="email"
                type="email"
                name="email"
                value="{{ old('email') }}"
                required
                autofocus
                placeholder="Enter your email"
                class="form-input"
            />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <div class="form-group">
            <label for="password" class="form-label">
                Password
            </label>
            <div class="password-wrapper">
                <input
                    id="password"
                    type="password"
                    name="password"
                    required
                    placeholder="Enter your password"
                    class="form-input"
                />
                <button type="button" class="password-toggle" id="passwordToggle">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path class="eye-open" d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path>
                        <circle class="eye-open" cx="12" cy="12" r="3"></circle>
                        <path class="eye-closed" style="display:none;" d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19m-6.72-1.07a3 3 0 1 1-4.24-4.24"></path>
                        <line class="eye-closed" style="display:none;" x1="1" y1="1" x2="23" y2="23"></line>
                    </svg>
                </button>
            </div>
            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <div style="margin-top:16px; margin-bottom: 24px; display:flex; align-items:center; justify-content:space-between;">
            <div class="checkbox-wrapper">
                <input type="checkbox" name="remember" id="remember">
                <label for="remember">Remember Me</label>
            </div>

            @if (Route::has('password.request'))
                <a class="link" href="{{ route('password.request') }}">Forgot Password?</a>
            @endif
        </div>

        <button type="submit" class="btn-primary" id="submitBtn">
            Sign In
        </button>

        @if (Route::has('register'))
            <div style="text-align:center; margin-top:14px; color:var(--roast); font-size:14px;">
                Don't have an account?
                <a class="link" href="{{ route('register') }}">Sign Up</a>
            </div>
        @endif
    </form>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Password Toggle
            const passwordToggle = document.getElementById('passwordToggle');
            const passwordInput = document.getElementById('password');
            const eyeOpen = passwordToggle.querySelectorAll('.eye-open');
            const eyeClosed = passwordToggle.querySelectorAll('.eye-closed');

            passwordToggle.addEventListener('click', function() {
                if (passwordInput.type === 'password') {
                    passwordInput.type = 'text';
                    eyeOpen.forEach(el => el.style.display = 'none');
                    eyeClosed.forEach(el => el.style.display = 'block');
                } else {
                    passwordInput.type = 'password';
                    eyeOpen.forEach(el => el.style.display = 'block');
                    eyeClosed.forEach(el => el.style.display = 'none');
                }
            });



            // Form submission loading state
            const loginForm = document.getElementById('loginForm');
            const submitBtn = document.getElementById('submitBtn');

            loginForm.addEventListener('submit', function() {
                submitBtn.classList.add('loading');
                submitBtn.textContent = 'Signing In...';
            });

            // Input focus effects
            const inputs = document.querySelectorAll('.form-input');
            inputs.forEach(input => {
                input.addEventListener('focus', function() {
                    this.parentElement.style.transform = 'scale(1.02)';
                });
                
                input.addEventListener('blur', function() {
                    this.parentElement.style.transform = 'scale(1)';
                });
            });
        });
    </script>
</x-guest-layout>
