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
                    transition: all 0.3s ease;
                "
                onfocus="this.style.borderColor='#c8813a'; this.style.boxShadow='0 0 0 3px rgba(200,129,58,.1)'"
                onblur="this.style.borderColor='rgba(200,129,58,.45)'; this.style.boxShadow='none'"
            />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <div style="margin-top:18px; position:relative;">
            <label style="display:block; font-weight:800; font-size:14px; margin-bottom:8px; color:#3a1f14;">
                Password
            </label>
            <div style="position:relative;">
                <input
                    id="password"
                    type="password"
                    name="password"
                    required
                    placeholder="Enter your password"
                    style="
                        width:100%;
                        padding:14px 16px;
                        padding-right:50px;
                        border-radius:12px;
                        border:1px solid rgba(74,37,24,.18);
                        outline:none;
                        font-size:15px;
                        transition: all 0.3s ease;
                    "
                    class="password-input"
                    onfocus="this.style.borderColor='#c8813a'; this.style.boxShadow='0 0 0 3px rgba(200,129,58,.1)'"
                    onblur="this.style.borderColor='rgba(74,37,24,.18)'; this.style.boxShadow='none'"
                />
                <button 
                    type="button" 
                    onclick="togglePassword()"
                    style="
                        position:absolute;
                        right:12px;
                        top:50%;
                        transform:translateY(-50%);
                        background:none;
                        border:none;
                        cursor:pointer;
                        color:rgba(74,37,24,.6);
                        font-size:18px;
                        padding:4px;
                        transition: color 0.3s ease;
                        display:flex;
                        align-items:center;
                        justify-content:center;
                    "
                    onmouseover="this.style.color='#c8813a'"
                    onmouseout="this.style.color='rgba(74,37,24,.6)'"
                >
                    <svg id="eyeIcon" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path>
                        <circle cx="12" cy="12" r="3"></circle>
                    </svg>
                </button>
            </div>
            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <div style="margin-top:16px; display:flex; align-items:center; justify-content:space-between;">
            <label style="display:flex; align-items:center; gap:10px; color:rgba(74,37,24,.75); font-size:14px;">
                <input type="checkbox" name="remember" style="accent-color:#c8813a;">
                Remember Me
            </label>

            @if (Route::has('password.request'))
                <a class="link" href="{{ route('password.request') }}" style="transition: all 0.3s ease;" onmouseover="this.style.color='#d4a847'" onmouseout="this.style.color='#b86a2b'">Forgot Password?</a>
            @endif
        </div>

        <button type="submit" id="submitBtn"
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
                transition: all 0.3s ease;
                position:relative;
                overflow:hidden;
            "
            onmouseover="this.style.transform='translateY(-2px)'; this.style.boxShadow='0 16px 35px rgba(200,129,58,.45)'"
            onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='0 14px 30px rgba(200,129,58,.35)'"
        >
            <span id="btnText">Sign In</span>
            <span id="btnLoader" style="display:none;">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="animation: spin 1s linear infinite;">
                    <circle cx="12" cy="12" r="10" stroke="currentColor" stroke-opacity="0.25"/>
                    <path d="M12 2 A10 10 0 0 1 22 12" stroke="currentColor"/>
                </svg>
            </span>
        </button>

        @if (Route::has('register'))
            <div style="text-align:center; margin-top:14px; color:rgba(74,37,24,.75); font-size:14px;">
                Don't have an account?
                <a class="link" href="{{ route('register') }}" style="transition: all 0.3s ease;" onmouseover="this.style.color='#d4a847'" onmouseout="this.style.color='#b86a2b'">Sign Up</a>
            </div>
        @endif
    </form>
    
    <script>
        function togglePassword() {
            const passwordInput = document.getElementById('password');
            const eyeIcon = document.getElementById('eyeIcon');
            
            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                // Show eye-slash icon (password hidden)
                eyeIcon.innerHTML = `
                    <path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19m-6.72-1.07a3 3 0 1 1-4.24-4.24"></path>
                    <line x1="1" y1="1" x2="23" y2="23"></line>
                `;
            } else {
                passwordInput.type = 'password';
                // Show eye icon (password visible)
                eyeIcon.innerHTML = `
                    <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path>
                    <circle cx="12" cy="12" r="3"></circle>
                `;
            }
        }
        
        document.querySelector('form').addEventListener('submit', function(e) {
            const submitBtn = document.getElementById('submitBtn');
            const btnText = document.getElementById('btnText');
            const btnLoader = document.getElementById('btnLoader');
            
            // Show loading state
            btnText.style.display = 'none';
            btnLoader.style.display = 'inline-block';
            submitBtn.disabled = true;
            submitBtn.style.opacity = '0.7';
            submitBtn.style.cursor = 'not-allowed';
        });
    </script>
</x-guest-layout>
