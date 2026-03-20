<x-guest-layout>
    <div style="width:100%; max-width:560px; margin:20px auto; padding:28px; border-radius:18px; background:rgba(15, 23, 42, 0.72); border:1px solid rgba(255,255,255,0.14); box-shadow:0 16px 45px rgba(0,0,0,0.38); backdrop-filter:blur(12px); -webkit-backdrop-filter:blur(12px); color:#e2e8f0;">
        <h1 style="margin:0 0 8px; font-size:26px; line-height:1.2; color:#f8fafc;">Complete Your Registration</h1>
        <p style="margin:0 0 10px; color:#cbd5e1; line-height:1.6;">
            You are signing in with <strong style="color:#f1f5f9;">{{ $providerLabel }}</strong> as
            <strong style="color:#f1f5f9;">{{ $displayName }}</strong>
            @if ($email)
                ({{ $email }})
            @endif
            .
        </p>
        <p style="margin:0 0 18px; color:#94a3b8; line-height:1.6;">
            Please review and accept the legal terms before we create your account.
        </p>

        <form method="POST" action="{{ route('social.agree.store') }}">
            @csrf
            <input type="hidden" name="provider" value="{{ $provider }}">

            <label for="terms_accepted" style="display:flex; gap:10px; align-items:flex-start; margin:0 0 12px; cursor:pointer;">
                <input id="terms_accepted" name="terms_accepted" type="checkbox" value="1" required style="margin-top:2px; width:16px; height:16px; accent-color:#38bdf8;">
                <span style="line-height:1.5; color:#e2e8f0;">
                    I agree to the
                    <a href="/terms" target="_blank" rel="noopener" style="color:#7dd3fc; text-decoration:underline;">Terms &amp; Conditions</a>
                    and
                    <a href="/privacy" target="_blank" rel="noopener" style="color:#7dd3fc; text-decoration:underline;">Privacy Policy</a>.
                </span>
            </label>

            @error('terms_accepted')
                <p style="margin:0 0 14px; color:#fca5a5; font-size:13px;">{{ $message }}</p>
            @enderror

            <div style="display:flex; gap:10px; flex-wrap:wrap; margin-top:18px;">
                <button type="submit" style="border:1px solid rgba(56, 189, 248, 0.55); background:rgba(56, 189, 248, 0.24); color:#e0f2fe; border-radius:10px; padding:10px 14px; font-size:14px; font-weight:700; cursor:pointer;">
                    Agree and Continue
                </button>
            </div>
        </form>

        <form method="POST" action="{{ route('social.agree.decline') }}" style="margin-top:10px;">
            @csrf
            <input type="hidden" name="provider" value="{{ $provider }}">
            <button type="submit" style="border:1px solid rgba(248, 113, 113, 0.35); background:rgba(248, 113, 113, 0.12); color:#fecaca; border-radius:10px; padding:9px 13px; font-size:13px; font-weight:600; cursor:pointer;">
                Decline and Return to Login
            </button>
        </form>
    </div>
</x-guest-layout>
