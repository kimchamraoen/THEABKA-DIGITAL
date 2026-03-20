<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Jobs\SendAdminNewUserNotificationJob;
use App\Models\SocialAccount;
use App\Models\SocialSetting;
use App\Models\User;
use App\Services\AdminEmailResolver;
use App\Services\UserTrackingService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Str;
use Laravel\Socialite\Facades\Socialite;

class SocialAuthController extends Controller
{
    /**
     * Supported OAuth providers.
     */
    protected array $providers = ['google', 'facebook', 'twitter'];

    /**
     * Redirect to the OAuth provider.
     */
    public function redirect(string $provider)
    {
        if (!in_array($provider, $this->providers)) {
            abort(404, 'Provider not supported.');
        }

        if (!SocialSetting::isProviderConfigured($provider)) {
            return redirect()->route('login')
                ->with('status', 'Social login with ' . ucfirst($provider) . ' is not configured.');
        }

        if (SocialSetting::get(strtoupper($provider) . '_ENABLED', 'true') !== 'true') {
            return redirect()->route('login')
                ->with('status', 'This login method is not available.');
        }

        $this->configureProvider($provider);

        return Socialite::driver($provider)->redirect();
    }

    /**
     * Handle the OAuth callback.
     *
     * A) If user is already logged in → CONNECT mode (link social account).
     * B) If user is NOT logged in → LOGIN mode (find or create user).
     */
    public function callback(string $provider, Request $request, UserTrackingService $trackingService)
    {
        if (!in_array($provider, $this->providers)) {
            abort(404, 'Provider not supported.');
        }

        if (SocialSetting::get(strtoupper($provider) . '_ENABLED', 'true') !== 'true') {
            return redirect()->route('login')
                ->with('status', 'This login method is not available.');
        }

        $this->configureProvider($provider);

        try {
            $socialUser = Socialite::driver($provider)->user();
        } catch (\Exception $e) {
            $redirectTo = Auth::check() ? route('profile.show') : route('login');
            return redirect($redirectTo)
                ->with('status', 'Unable to authenticate with ' . ucfirst($provider) . '. Please try again.');
        }

        $providerId = $socialUser->getId();
        $avatarUrl = $socialUser->getAvatar();

        // A) CONNECT MODE — user is already logged in
        if (Auth::check()) {
            return $this->connectProvider(Auth::user(), $provider, $providerId, $avatarUrl);
        }

        // B) LOGIN MODE — user is not logged in
        return $this->loginViaProvider($socialUser, $provider, $providerId, $avatarUrl, $request, $trackingService);
    }

    /**
     * Handle Telegram login callback.
     * Telegram sends auth data via query parameters.
     */
    public function telegramCallback(Request $request, UserTrackingService $trackingService)
    {
        if (!SocialSetting::isProviderConfigured('telegram')) {
            return redirect()->route('login')
                ->with('status', 'Telegram login is not configured.');
        }

        if (SocialSetting::get('TELEGRAM_ENABLED', 'true') !== 'true') {
            return redirect()->route('login')
                ->with('status', 'This login method is not available.');
        }

        $botToken = SocialSetting::get('TELEGRAM_BOT_TOKEN');

        if (!$this->verifyTelegramAuth($request->all(), $botToken)) {
            return redirect()->route('login')
                ->with('status', 'Invalid Telegram authentication.');
        }

        $telegramId = $request->input('id');
        $firstName = $request->input('first_name', '');
        $lastName = $request->input('last_name', '');
        $username = $request->input('username', '');
        $photoUrl = $request->input('photo_url');

        // A) CONNECT MODE — user is already logged in
        if (Auth::check()) {
            return $this->connectProvider(Auth::user(), 'telegram', $telegramId, $photoUrl);
        }

        // B) LOGIN MODE
        $socialAccount = SocialAccount::where('provider', 'telegram')
            ->where('provider_id', $telegramId)
            ->first();

        if ($socialAccount) {
            // Update avatar if changed
            if ($photoUrl && $socialAccount->avatar !== $photoUrl) {
                $socialAccount->update(['avatar' => $photoUrl]);
            }
            // Update user avatar if this is the active avatar source
            $user = $socialAccount->user;
            if ($user->avatar_provider === 'telegram' && $photoUrl) {
                $user->update(['avatar' => $photoUrl]);
            }
            return $this->completeSocialLogin($user, 'telegram', $request, $trackingService);
        }

        // No existing social account — create new user + social account
        $name = trim($firstName . ' ' . $lastName) ?: $username ?: 'Telegram User';

        $this->storePendingSocialRegistration([
            'provider' => 'telegram',
            'provider_id' => $telegramId,
            'name' => $name,
            'email' => null,
            'avatar' => $photoUrl,
            'email_verified_at' => now(),
        ]);

        return redirect()->route('social.agree', ['provider' => 'telegram']);
    }

    /**
     * CONNECT MODE: Link a social provider to the currently logged-in user.
     */
    protected function connectProvider(User $user, string $provider, string $providerId, ?string $avatarUrl)
    {
        // Check if this social account is already linked to another user
        $existing = SocialAccount::where('provider', $provider)
            ->where('provider_id', $providerId)
            ->first();

        if ($existing && $existing->user_id !== $user->id) {
            return redirect()->route('profile.show')
                ->with('connected_accounts_error', 'This ' . ucfirst($provider === 'twitter' ? 'X' : $provider) . ' account is already connected to another user.');
        }

        // Find or create the social account for this user
        $socialAccount = SocialAccount::updateOrCreate(
            ['provider' => $provider, 'provider_id' => $providerId],
            ['user_id' => $user->id, 'avatar' => $avatarUrl]
        );

        // If this is the user's first social account and they have no avatar, set it automatically
        if (!$user->avatar && $avatarUrl) {
            $user->update([
                'avatar' => $avatarUrl,
                'avatar_provider' => $provider,
            ]);
        }

        $providerLabel = $provider === 'twitter' ? 'X (Twitter)' : ucfirst($provider);

        // Flash data for avatar choice prompt if user already has an avatar
        // and the new provider has a different one
        if ($user->avatar && $avatarUrl && $user->avatar !== $avatarUrl) {
            session()->flash('new_social_avatar', [
                'provider' => $provider,
                'avatar_url' => $avatarUrl,
            ]);
        }

        return redirect()->route('profile.show')
            ->with('connected_accounts_status', "Your {$providerLabel} account has been connected.");
    }

    /**
     * LOGIN MODE: Find existing user via social account or email, or create new.
     */
    protected function loginViaProvider(
        object $socialUser,
        string $provider,
        string $providerId,
        ?string $avatarUrl,
        Request $request,
        UserTrackingService $trackingService
    ): \Illuminate\Http\RedirectResponse
    {
        // 1. Find by social account
        $socialAccount = SocialAccount::where('provider', $provider)
            ->where('provider_id', $providerId)
            ->first();

        if ($socialAccount) {
            // Update avatar if changed
            if ($avatarUrl && $socialAccount->avatar !== $avatarUrl) {
                $socialAccount->update(['avatar' => $avatarUrl]);
            }
            $user = $socialAccount->user;
            if ($user->avatar_provider === $provider && $avatarUrl) {
                $user->update(['avatar' => $avatarUrl]);
            }
            return $this->completeSocialLogin($user, $provider, $request, $trackingService);
        }

        // 2. Find by email match
        if ($socialUser->getEmail()) {
            $user = User::where('email', $socialUser->getEmail())->first();

            if ($user) {
                // Create social account link
                $user->socialAccounts()->create([
                    'provider' => $provider,
                    'provider_id' => $providerId,
                    'avatar' => $avatarUrl,
                ]);
                // Set avatar if user doesn't have one
                if (!$user->avatar && $avatarUrl) {
                    $user->update([
                        'avatar' => $avatarUrl,
                        'avatar_provider' => $provider,
                    ]);
                }
                return $this->completeSocialLogin($user, $provider, $request, $trackingService);
            }
        }

        // 3. New social account: collect legal agreement before creating user
        $this->storePendingSocialRegistration([
            'provider' => $provider,
            'provider_id' => $providerId,
            'name' => $socialUser->getName() ?: $socialUser->getNickname() ?: 'User',
            'email' => $socialUser->getEmail(),
            'avatar' => $avatarUrl,
            'email_verified_at' => $socialUser->getEmail() ? now() : null,
        ]);

        return redirect()->route('social.agree', ['provider' => $provider]);
    }

    public function showAgreement(Request $request)
    {
        $provider = (string) $request->query('provider', '');

        $pending = $this->getPendingSocialRegistration($provider);
        if (!$pending) {
            return redirect()->route('login')
                ->with('status', 'Your social login session expired. Please try again.');
        }

        return view('auth.social-agree', [
            'provider' => $provider,
            'providerLabel' => $provider === 'twitter' ? 'X (Twitter)' : ucfirst($provider),
            'displayName' => $pending['name'] ?? 'User',
            'email' => $pending['email'] ?? null,
        ]);
    }

    public function processAgreement(Request $request, UserTrackingService $trackingService)
    {
        $validated = $request->validate([
            'provider' => ['required', 'in:google,facebook,twitter,telegram'],
            'terms_accepted' => ['required', 'accepted'],
        ]);

        $provider = $validated['provider'];
        $pending = $this->getPendingSocialRegistration($provider);

        if (!$pending) {
            return redirect()->route('login')
                ->with('status', 'Your social login session expired. Please try again.');
        }

        $cookieConsent = $request->cookie('cookie_consent', 'pending');
        if (!in_array($cookieConsent, ['accepted', 'declined', 'pending'], true)) {
            $cookieConsent = 'pending';
        }

        $agreementTimestamp = now();
        $cookieConsentAt = $cookieConsent === 'pending' ? null : $agreementTimestamp;

        $user = User::create([
            'name' => $pending['name'],
            'email' => $pending['email'],
            'password' => Hash::make(Str::random(24)),
            'avatar' => $pending['avatar'],
            'avatar_provider' => $provider,
            'email_verified_at' => $pending['email_verified_at'] ?? null,
            'terms_accepted' => true,
            'terms_accepted_at' => $agreementTimestamp,
            'privacy_accepted' => true,
            'privacy_accepted_at' => $agreementTimestamp,
            'cookie_consent' => $cookieConsent,
            'cookie_consent_at' => $cookieConsentAt,
            'agreement_ip' => $request->ip(),
        ]);

        $user->socialAccounts()->create([
            'provider' => $provider,
            'provider_id' => $pending['provider_id'],
            'avatar' => $pending['avatar'],
        ]);

        $this->clearPendingSocialRegistration();

        $this->dispatchAdminRegistrationNotification($user, $request, $provider);

        return $this->completeSocialLogin($user, $provider, $request, $trackingService);
    }

    public function declineAgreement(Request $request)
    {
        $provider = (string) $request->input('provider', '');

        if ($provider !== '') {
            $pending = $this->getPendingSocialRegistration($provider);
            if (!$pending) {
                return redirect()->route('login')
                    ->with('status', 'Your social login session expired. Please try again.');
            }
        }

        $this->clearPendingSocialRegistration();

        return redirect()->route('login')
            ->with('status', 'Registration was cancelled because terms were not accepted.');
    }

    protected function storePendingSocialRegistration(array $payload): void
    {
        session(['social_auth_pending' => $payload]);
    }

    protected function getPendingSocialRegistration(string $provider): ?array
    {
        $pending = session('social_auth_pending');

        if (!is_array($pending)) {
            return null;
        }

        if (($pending['provider'] ?? null) !== $provider) {
            return null;
        }

        return $pending;
    }

    protected function clearPendingSocialRegistration(): void
    {
        session()->forget('social_auth_pending');
    }

    protected function completeSocialLogin(
        User $user,
        string $provider,
        Request $request,
        UserTrackingService $trackingService
    ): \Illuminate\Http\RedirectResponse {
        $user->forceFill(['login_provider' => $provider])->save();

        if ($this->requiresTwoFactorChallenge($user)) {
            // Reuse Fortify's native pending-login session keys and challenge route.
            $request->session()->put([
                'login.id' => $user->getKey(),
                'login.remember' => true,
            ]);

            return redirect()->route('two-factor.login');
        }

        Auth::login($user, true);
        $trackingService->track($user, $request);

        return redirect()->intended('/dashboard');
    }

    protected function requiresTwoFactorChallenge(User $user): bool
    {
        if (empty($user->two_factor_secret)) {
            return false;
        }

        if (!in_array(\Laravel\Fortify\TwoFactorAuthenticatable::class, class_uses_recursive($user), true)) {
            return false;
        }

        if (\Laravel\Fortify\Fortify::confirmsTwoFactorAuthentication()) {
            return !is_null($user->two_factor_confirmed_at);
        }

        return true;
    }

    protected function dispatchAdminRegistrationNotification(User $user, Request $request, string $provider): void
    {
        $adminEmail = app(AdminEmailResolver::class)->resolve();

        if (! $adminEmail) {
            return;
        }

        $viewUserUrl = Route::has('admin.users.show')
            ? route('admin.users.show', $user->id)
            : route('admin.users', ['user' => $user->id]);

        dispatch(new SendAdminNewUserNotificationJob(
            recipientEmail: $adminEmail,
            payload: [
                'name' => $user->name,
                'email' => $user->email ?: 'N/A',
                'view_user_url' => $viewUserUrl,
                'registered_at' => now()->timezone('Asia/Phnom_Penh')->format('Y-m-d H:i:s') . ' (Asia/Phnom_Penh)',
            ],
            ipAddress: $request->ip(),
            userAgent: $request->userAgent(),
            provider: $provider,
        ));
    }

    /**
     * Configure the OAuth provider dynamically from database settings.
     */
    protected function configureProvider(string $provider): void
    {
        $config = match ($provider) {
            'google' => [
                'client_id' => SocialSetting::get('GOOGLE_CLIENT_ID'),
                'client_secret' => SocialSetting::get('GOOGLE_CLIENT_SECRET'),
                'redirect' => SocialSetting::get('GOOGLE_REDIRECT_URL'),
            ],
            'facebook' => [
                'client_id' => SocialSetting::get('FACEBOOK_CLIENT_ID'),
                'client_secret' => SocialSetting::get('FACEBOOK_CLIENT_SECRET'),
                'redirect' => SocialSetting::get('FACEBOOK_REDIRECT_URL'),
            ],
            'twitter' => [
                'client_id' => SocialSetting::get('TWITTER_CLIENT_ID'),
                'client_secret' => SocialSetting::get('TWITTER_CLIENT_SECRET'),
                'redirect' => SocialSetting::get('TWITTER_REDIRECT_URL'),
                'oauth' => 2,
            ],
            default => [],
        };

        config(["services.{$provider}" => $config]);
    }

    /**
     * Verify Telegram authentication hash.
     * @see https://core.telegram.org/widgets/login#checking-authorization
     */
    protected function verifyTelegramAuth(array $authData, string $botToken): bool
    {
        if (empty($authData['hash'])) {
            return false;
        }

        $checkHash = $authData['hash'];
        unset($authData['hash']);

        // Sort array by key
        ksort($authData);

        // Create data-check-string
        $dataCheckArr = [];
        foreach ($authData as $key => $value) {
            $dataCheckArr[] = $key . '=' . $value;
        }
        $dataCheckString = implode("\n", $dataCheckArr);

        // Create secret key from bot token
        $secretKey = hash('sha256', $botToken, true);

        // Calculate hash
        $hash = hash_hmac('sha256', $dataCheckString, $secretKey);

        // Verify hash matches
        if ($hash !== $checkHash) {
            return false;
        }

        // Check if auth_date is not too old (allow 1 day)
        if (isset($authData['auth_date']) && (time() - (int)$authData['auth_date']) > 86400) {
            return false;
        }

        return true;
    }

    /**
     * Handle Facebook deauthorize callback.
     * Facebook sends a signed_request when a user deauthorizes the app.
     */
    public function facebookDeauthorize(Request $request)
    {
        $signedRequest = $request->input('signed_request');

        if (!$signedRequest) {
            return response()->json(['error' => 'Missing signed_request'], 400);
        }

        $data = $this->parseSignedRequest($signedRequest);

        if (!$data) {
            return response()->json(['error' => 'Invalid signed_request'], 400);
        }

        $facebookUserId = $data['user_id'] ?? null;

        if (!$facebookUserId) {
            return response()->json(['error' => 'No user_id in signed_request'], 400);
        }

        $socialAccount = SocialAccount::where('provider', 'facebook')
            ->where('provider_id', $facebookUserId)
            ->first();

        if ($socialAccount) {
            $user = $socialAccount->user;

            // If this was the avatar source, clear it
            if ($user->avatar_provider === 'facebook') {
                $nextAvatar = $user->socialAccounts()
                    ->where('provider', '!=', 'facebook')
                    ->whereNotNull('avatar')
                    ->first();

                $user->update([
                    'avatar' => $nextAvatar?->avatar,
                    'avatar_provider' => $nextAvatar?->provider,
                ]);
            }

            $socialAccount->delete();

            Log::info('Facebook deauthorization processed', [
                'user_id' => $user->id,
                'facebook_user_id' => $facebookUserId,
            ]);
        } else {
            Log::info('Facebook deauthorization: no matching user found', [
                'facebook_user_id' => $facebookUserId,
            ]);
        }

        $confirmationCode = Str::uuid()->toString();

        return response()->json([
            'url' => url('/'),
            'confirmation_code' => $confirmationCode,
        ]);
    }

    /**
     * Parse Facebook signed_request.
     */
    private function parseSignedRequest(string $signedRequest): ?array
    {
        $appSecret = SocialSetting::get('FACEBOOK_CLIENT_SECRET');

        if (!$appSecret) {
            return null;
        }

        $parts = explode('.', $signedRequest, 2);

        if (count($parts) !== 2) {
            return null;
        }

        [$encodedSig, $payload] = $parts;

        $sig = base64_decode(strtr($encodedSig, '-_', '+/'));
        $data = json_decode(base64_decode(strtr($payload, '-_', '+/')), true);

        if (!$data || !isset($data['algorithm']) || strtoupper($data['algorithm']) !== 'HMAC-SHA256') {
            return null;
        }

        $expectedSig = hash_hmac('sha256', $payload, $appSecret, true);

        if (!hash_equals($expectedSig, $sig)) {
            return null;
        }

        return $data;
    }
}
