<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Log;
use Laravel\Fortify\TwoFactorAuthenticatable;
use Laravel\Jetstream\HasProfilePhoto;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable implements MustVerifyEmail
{
    use HasApiTokens;

    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory;
    use HasProfilePhoto;
    use Notifiable;
    use TwoFactorAuthenticatable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'theme_preference',
        'glass_style_preference',
        'avatar',
        'avatar_provider',
        'bypass_email_verification',
        'login_provider',
        'last_login_at',
        'last_login_ip',
        'last_login_device',
        'last_login_browser',
        'last_login_os',
        'terms_accepted',
        'terms_accepted_at',
        'privacy_accepted',
        'privacy_accepted_at',
        'cookie_consent',
        'cookie_consent_at',
        'agreement_ip',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
        'two_factor_recovery_codes',
        'two_factor_secret',
    ];

    /**
     * The accessors to append to the model's array form.
     *
     * @var array<int, string>
     */
    protected $appends = [
        'profile_photo_url',
        'profile_image',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'bypass_email_verification' => 'boolean',
            'last_login_at' => 'datetime',
            'terms_accepted' => 'boolean',
            'terms_accepted_at' => 'datetime',
            'privacy_accepted' => 'boolean',
            'privacy_accepted_at' => 'datetime',
            'cookie_consent_at' => 'datetime',
        ];
    }

    /**
     * Get the user's profile image URL.
     * Priority: uploaded photo > user avatar > any social avatar > UI Avatars fallback.
     */
    public function getProfileImageAttribute(): string
    {
        // 1. User-uploaded profile photo takes highest priority
        if (!empty($this->profile_photo_path)) {
            return $this->profile_photo_url;
        }

        // 2. Explicit avatar (set from a social provider or manually)
        if (!empty($this->avatar)) {
            return $this->avatar;
        }

        // 3. First available social avatar
        $socialAvatar = $this->socialAccounts()->whereNotNull('avatar')->value('avatar');
        if ($socialAvatar) {
            return $socialAvatar;
        }

        // 4. Fallback to generated initials avatar
        $name = urlencode($this->name ?? 'User');

        return "https://ui-avatars.com/api/?name={$name}&background=random";
    }

    public function socialAccounts(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(SocialAccount::class);
    }

    public function userLoginLogs(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(UserLoginLog::class);
    }

    /**
     * Determine if the user is a super admin.
     */
    public function isSuperAdmin(): bool
    {
        return $this->role === 'super_admin';
    }

    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    public function canBroadcast(): bool
    {
        return in_array($this->role, ['super_admin', 'admin'], true);
    }

    /**
     * Determine if the user can bypass email verification check.
     * Returns true if:
     * - User has verified their email
     * - User has the bypass flag set
     * - Global setting allows unverified login
     */
    public function canBypassEmailVerification(): bool
    {
        // Already verified
        if ($this->hasVerifiedEmail()) {
            return true;
        }

        // Per-user bypass flag
        if ($this->bypass_email_verification) {
            return true;
        }

        // Global setting
        $settings = Setting::instance();
        if ($settings->allow_unverified_login) {
            return true;
        }

        return false;
    }

    public function broadcastNotifications(): \Illuminate\Database\Eloquent\Relations\BelongsToMany
    {
        return $this->belongsToMany(BroadcastNotification::class, 'broadcast_notification_user')
            ->withPivot('read_at')
            ->withTimestamps()
            ->orderByDesc('broadcast_notifications.created_at');
    }

    public function unreadBroadcastCount(): int
    {
        return $this->broadcastNotifications()->whereNull('broadcast_notification_user.read_at')->count();
    }

    /**
     * Get the effective theme for this user.
     * User preference takes priority, otherwise system default.
     */
    public function getEffectiveTheme(): string
    {
        if ($this->theme_preference) {
            return $this->theme_preference;
        }

        return Setting::first()?->default_theme ?? 'dark';
    }

    public function chatConversations(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(ChatConversation::class);
    }

    public function chatbotSettings(): \Illuminate\Database\Eloquent\Relations\HasOne
    {
        return $this->hasOne(UserChatbotSettings::class);
    }

    /**
     * Get the effective glass style for this user.
     */
    public function getEffectiveGlassStyle(): string
    {
        $allowedStyles = ['liquid', 'card', 'crystal', 'frosted', 'glass3d'];

        if (in_array($this->glass_style_preference, $allowedStyles, true)) {
            return $this->glass_style_preference;
        }

        $defaultStyle = Setting::instance()->default_glass_style ?? 'liquid';

        if (in_array($defaultStyle, $allowedStyles, true)) {
            return $defaultStyle;
        }

        return 'liquid';
    }

    /**
     * Send the email verification notification.
     * Dispatched after the response so SMTP latency doesn't block the page.
     */
    public function sendEmailVerificationNotification(): void
    {
        $user = $this;

        app()->terminating(function () use ($user) {
            try {
                // Call parent's implementation after the HTTP response is sent
                $user->notify(new \Illuminate\Auth\Notifications\VerifyEmail);
            } catch (\Exception $e) {
                Log::warning('Failed to send verification email: ' . $e->getMessage(), [
                    'user_id' => $user->id,
                    'email' => $user->email,
                ]);
            }
        });
    }
}
