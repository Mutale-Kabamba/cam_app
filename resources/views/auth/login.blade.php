@extends('layouts.app')

@section('title', 'Official Portal Login - Admin & Judges')

@section('content')
<div style="max-width: 500px; margin: 2.5rem auto 4rem auto;">
    <!-- Brand Title Badge -->
    <div style="text-align: center; margin-bottom: 2rem;">
        <div style="display: inline-flex; align-items: center; gap: 0.5rem; background: rgba(245, 158, 11, 0.12); border: 1px solid rgba(245, 158, 11, 0.3); padding: 0.4rem 1rem; border-radius: 9999px; color: #f59e0b; font-size: 0.8rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.05em; margin-bottom: 1rem;">
            <span>🔐 Restricted Access Portal</span>
        </div>
        <h2 style="font-family: var(--font-display); font-size: 2.3rem; font-weight: 800; color: #fff; letter-spacing: -0.02em; margin-bottom: 0.5rem;">
            Official Portal Login
        </h2>
        <p style="color: var(--text-muted); font-size: 0.95rem; line-height: 1.5;">
            Unified sign-in for Diocesan Executive Administrators and Official Adjudication Judges.
        </p>
    </div>

    <!-- Login Glass Card -->
    <div class="glass-card" style="border: 1px solid rgba(245, 158, 11, 0.25); background: linear-gradient(135deg, rgba(23, 31, 50, 0.85), rgba(15, 23, 42, 0.95)); padding: 2rem;">
        <form method="POST" action="{{ route('login') }}" id="login-form">
            @csrf

            <!-- Email Input -->
            <div style="margin-bottom: 1.4rem;">
                <label for="email" style="display: block; font-size: 0.8rem; font-weight: 700; color: #cbd5e1; text-transform: uppercase; letter-spacing: 0.04em; margin-bottom: 0.45rem;">
                    Email Address
                </label>
                <div style="position: relative;">
                    <span style="position: absolute; left: 1rem; top: 50%; transform: translateY(-50%); font-size: 1.1rem; color: var(--text-muted);">📧</span>
                    <input type="email" 
                           id="email" 
                           name="email" 
                           value="{{ old('email') }}" 
                           required 
                           autofocus 
                           placeholder="e.g. admin@camfestival.org or judge1@camfestival.org" 
                           style="width: 100%; padding-left: 2.85rem; font-size: 0.95rem; height: 46px;">
                </div>
            </div>

            <!-- Password Input -->
            <div style="margin-bottom: 1.4rem;">
                <label for="password" style="display: block; font-size: 0.8rem; font-weight: 700; color: #cbd5e1; text-transform: uppercase; letter-spacing: 0.04em; margin-bottom: 0.45rem;">
                    Password
                </label>
                <div style="position: relative;">
                    <span style="position: absolute; left: 1rem; top: 50%; transform: translateY(-50%); font-size: 1.1rem; color: var(--text-muted);">🔑</span>
                    <input type="password" 
                           id="password" 
                           name="password" 
                           required 
                           placeholder="••••••••" 
                           style="width: 100%; padding-left: 2.85rem; font-size: 0.95rem; height: 46px;">
                </div>
            </div>

            <!-- Remember Me -->
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.75rem;">
                <label style="display: flex; align-items: center; gap: 0.5rem; cursor: pointer; font-size: 0.875rem; color: #cbd5e1;">
                    <input type="checkbox" name="remember" id="remember" style="accent-color: #f59e0b; width: 16px; height: 16px;">
                    Keep me signed in
                </label>
            </div>

            <!-- Submit Button -->
            <button type="submit" class="btn btn-primary" style="width: 100%; height: 48px; font-size: 1rem; font-weight: 800; letter-spacing: 0.02em;">
                Sign In to Official Workstation &rarr;
            </button>
        </form>

        <!-- Quick Demo Switcher Pills for Testing -->
        <div style="margin-top: 2rem; pt-4; border-top: 1px solid var(--border-card); padding-top: 1.5rem;">
            <div style="font-size: 0.75rem; font-weight: 700; color: var(--text-muted); text-transform: uppercase; letter-spacing: 0.05em; text-align: center; margin-bottom: 0.75rem;">
                ⚡ Quick Select Credentials (Testing & Evaluation)
            </div>
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 0.6rem;">
                <button type="button" 
                        class="btn btn-secondary btn-sm" 
                        onclick="fillCredentials('admin@camfestival.org', 'password')"
                        style="justify-content: flex-start; text-align: left; padding: 0.55rem 0.75rem;">
                    🛡️ <div><strong style="display:block; font-size:0.8rem; color:#93c5fd;">Admin</strong><span style="font-size:0.7rem; color:var(--text-muted);">Executive Committee</span></div>
                </button>
                <button type="button" 
                        class="btn btn-secondary btn-sm" 
                        onclick="fillCredentials('judge1@camfestival.org', 'password')"
                        style="justify-content: flex-start; text-align: left; padding: 0.55rem 0.75rem;">
                    ⚖️ <div><strong style="display:block; font-size:0.8rem; color:#f59e0b;">Judge 1</strong><span style="font-size:0.7rem; color:var(--text-muted);">Adjudicator 1</span></div>
                </button>
                <button type="button" 
                        class="btn btn-secondary btn-sm" 
                        onclick="fillCredentials('judge2@camfestival.org', 'password')"
                        style="justify-content: flex-start; text-align: left; padding: 0.55rem 0.75rem;">
                    ⚖️ <div><strong style="display:block; font-size:0.8rem; color:#f59e0b;">Judge 2</strong><span style="font-size:0.7rem; color:var(--text-muted);">Adjudicator 2</span></div>
                </button>
                <button type="button" 
                        class="btn btn-secondary btn-sm" 
                        onclick="fillCredentials('judge3@camfestival.org', 'password')"
                        style="justify-content: flex-start; text-align: left; padding: 0.55rem 0.75rem;">
                    ⚖️ <div><strong style="display:block; font-size:0.8rem; color:#f59e0b;">Judge 3</strong><span style="font-size:0.7rem; color:var(--text-muted);">Adjudicator 3</span></div>
                </button>
            </div>
        </div>
    </div>
</div>

<script>
function fillCredentials(email, password) {
    document.getElementById('email').value = email;
    document.getElementById('password').value = password;
    document.getElementById('login-form').submit();
}
</script>
@endsection
