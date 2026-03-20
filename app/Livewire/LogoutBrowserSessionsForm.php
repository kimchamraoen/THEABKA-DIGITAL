<?php

namespace App\Livewire;

use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use Laravel\Jetstream\Agent;
use Laravel\Jetstream\Http\Livewire\LogoutOtherBrowserSessionsForm;

class LogoutBrowserSessionsForm extends LogoutOtherBrowserSessionsForm
{
    /**
     * Indicates if a single session logout is being confirmed.
     */
    public $confirmingSingleLogout = false;

    /**
     * The session ID to log out.
     */
    public $sessionIdToLogout = null;

    /**
     * The password for single session logout.
     */
    public $singleSessionPassword = '';

    /**
     * Confirm that the user would like to log out a specific session.
     */
    public function confirmSingleLogout(string $sessionId)
    {
        $this->singleSessionPassword = '';
        $this->sessionIdToLogout = $sessionId;
        $this->confirmingSingleLogout = true;

        $this->dispatch('confirming-single-session-logout');
    }

    /**
     * Log out a specific browser session.
     */
    public function logoutSingleSession()
    {
        if (config('session.driver') !== 'database') {
            return;
        }

        $this->resetErrorBag();

        if (! Hash::check($this->singleSessionPassword, Auth::user()->password)) {
            throw ValidationException::withMessages([
                'single_session_password' => [__('This password does not match our records.')],
            ]);
        }

        DB::connection(config('session.connection'))
            ->table(config('session.table', 'sessions'))
            ->where('id', $this->sessionIdToLogout)
            ->where('user_id', Auth::user()->getAuthIdentifier())
            ->delete();

        $this->confirmingSingleLogout = false;
        $this->sessionIdToLogout = null;
        $this->singleSessionPassword = '';

        $this->dispatch('loggedOut');
    }

    /**
     * Get the current sessions (including session ID for per-device logout).
     */
    public function getSessionsProperty()
    {
        if (config('session.driver') !== 'database') {
            return collect();
        }

        return collect(
            DB::connection(config('session.connection'))
                ->table(config('session.table', 'sessions'))
                ->where('user_id', Auth::user()->getAuthIdentifier())
                ->orderBy('last_activity', 'desc')
                ->get()
        )->map(function ($session) {
            return (object) [
                'id' => $session->id,
                'agent' => $this->createAgent($session),
                'ip_address' => $session->ip_address,
                'is_current_device' => $session->id === request()->session()->getId(),
                'last_active' => Carbon::createFromTimestamp($session->last_activity)->diffForHumans(),
            ];
        });
    }

    /**
     * Create a new agent instance from the given session.
     */
    protected function createAgent($session)
    {
        return tap(new Agent(), fn ($agent) => $agent->setUserAgent($session->user_agent));
    }
}
