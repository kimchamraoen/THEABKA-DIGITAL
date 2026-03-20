<?php

namespace App\Livewire;

use App\Models\SocialAccount;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Livewire\Component;

class ConnectedAccounts extends Component
{
    public bool $confirmingDisconnect = false;
    public string $disconnectingProvider = '';
    public string $password = '';
    public bool $needsPassword = false;

    // Avatar choice modal
    public bool $showAvatarChoiceModal = false;
    public array $avatarOptions = [];

    protected array $providers = ['google', 'facebook', 'twitter', 'telegram'];

    public function confirmDisconnect(string $provider): void
    {
        $this->resetErrorBag();
        $this->password = '';
        $this->disconnectingProvider = $provider;

        $user = Auth::user();

        // If user has no password and this is their last social account, require password
        $this->needsPassword = $this->isSocialOnlyAccount($user)
            && $user->socialAccounts()->count() === 1;

        $this->confirmingDisconnect = true;
    }

    public function disconnectProvider(): void
    {
        $this->resetErrorBag();

        $user = Auth::user();
        $provider = $this->disconnectingProvider;

        // If social-only and last account, require password first
        if ($this->isSocialOnlyAccount($user) && $user->socialAccounts()->count() === 1) {
            if (empty($this->password)) {
                $this->addError('password', __('app.profile.password_required_disconnect'));
                return;
            }
            $user->update(['password' => Hash::make($this->password)]);
        }

        // Delete the specific social account record
        $user->socialAccounts()->where('provider', $provider)->delete();

        // If the disconnected provider was the avatar source, auto-switch
        if ($user->avatar_provider === $provider) {
            $nextSocial = $user->socialAccounts()->whereNotNull('avatar')->first();

            if ($nextSocial) {
                $user->update([
                    'avatar' => $nextSocial->avatar,
                    'avatar_provider' => $nextSocial->provider,
                ]);
            } else {
                $user->update([
                    'avatar' => null,
                    'avatar_provider' => null,
                ]);
            }

            $providerLabel = $provider === 'twitter' ? 'X (Twitter)' : ucfirst($provider);
            session()->flash('connected_accounts_status', __('app.profile.provider_disconnected') . ' ' . __('Your profile picture has been updated since you disconnected :provider.', ['provider' => $providerLabel]));
        } else {
            session()->flash('connected_accounts_status', __('app.profile.provider_disconnected'));
        }

        $this->confirmingDisconnect = false;
        $this->disconnectingProvider = '';
        $this->password = '';
    }

    /**
     * Set the user's avatar from a specific connected provider.
     */
    public function setAvatarFrom(string $provider): void
    {
        $user = Auth::user();
        $socialAccount = $user->socialAccounts()->where('provider', $provider)->first();

        if ($socialAccount && $socialAccount->avatar) {
            $user->update([
                'avatar' => $socialAccount->avatar,
                'avatar_provider' => $provider,
            ]);
        }

        $this->showAvatarChoiceModal = false;
    }

    /**
     * Reset avatar to default (UI Avatars initials).
     */
    public function useDefaultAvatar(): void
    {
        $user = Auth::user();
        $user->update([
            'avatar' => null,
            'avatar_provider' => null,
        ]);

        $this->showAvatarChoiceModal = false;
    }

    /**
     * Accept the new social avatar (from connect prompt).
     */
    public function acceptNewAvatar(string $provider, string $avatarUrl): void
    {
        $user = Auth::user();
        $user->update([
            'avatar' => $avatarUrl,
            'avatar_provider' => $provider,
        ]);
    }

    /**
     * Open the avatar picker modal.
     */
    public function openAvatarPicker(): void
    {
        $user = Auth::user();
        $this->avatarOptions = $user->socialAccounts()
            ->whereNotNull('avatar')
            ->get(['provider', 'avatar'])
            ->toArray();

        $this->showAvatarChoiceModal = true;
    }

    public function render()
    {
        $user = Auth::user();

        $connectedProviders = $user->socialAccounts()->pluck('provider')->toArray();

        return view('livewire.connected-accounts', [
            'providers' => $this->providers,
            'connectedProviders' => $connectedProviders,
            'user' => $user,
        ]);
    }

    private function isSocialOnlyAccount($user): bool
    {
        // A user is social-only if they have social accounts and no email
        // (or if we detect they never set a real password).
        return $user->socialAccounts()->exists() && !$user->email;
    }
}
