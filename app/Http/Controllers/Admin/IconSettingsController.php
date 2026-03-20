<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SiteIcon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\View\View;

class IconSettingsController extends Controller
{
    public function index(): View
    {
        SiteIcon::upsertDefaults($this->defaultIcons());

        return view('admin.settings.icons', [
            'icons' => SiteIcon::query()->orderBy('page')->orderBy('label')->get(),
        ]);
    }

    public function update(Request $request, string $key): RedirectResponse
    {
        $icon = SiteIcon::query()->where('key', $key)->firstOrFail();

        $validated = $request->validate([
            'label' => 'required|string|max:255',
            'page' => 'required|string|max:255',
            'icon_type' => 'required|in:css_class,svg,emoji,image',
            'icon_value' => 'nullable|string',
            // Max 512KB and max 128x128 for predictable rendering in nav/icon slots.
            'icon_image' => 'nullable|image|mimes:png,jpg,jpeg,webp|max:512|dimensions:max_width=128,max_height=128',
        ]);

        $iconValue = $validated['icon_value'] ?? null;

        if ($validated['icon_type'] === 'image' && $request->hasFile('icon_image')) {
            $iconValue = $request->file('icon_image')->store('icons', 'public');
        } elseif ($validated['icon_type'] === 'image' && empty($iconValue)) {
            $iconValue = $icon->icon_value;
        }

        $icon->update([
            'label' => $validated['label'],
            'page' => $validated['page'],
            'icon_type' => $validated['icon_type'],
            'icon_value' => $iconValue,
        ]);

        Cache::forget(SiteIcon::CACHE_KEY);

        return redirect()->route('admin.settings.icons')->with('status', 'Icon updated successfully.');
    }

    protected function defaultIcons(): array
    {
        return [
            ['key' => 'sidebar.dashboard', 'label' => 'Sidebar Dashboard', 'page' => 'Sidebar Navigation'],
            ['key' => 'sidebar.chatbot', 'label' => 'Sidebar Chatbot', 'page' => 'Sidebar Navigation'],
            ['key' => 'sidebar.profile', 'label' => 'Sidebar Profile', 'page' => 'Sidebar Navigation'],
            ['key' => 'sidebar.docs', 'label' => 'Sidebar Docs', 'page' => 'Sidebar Navigation'],
            ['key' => 'sidebar.analytics', 'label' => 'Sidebar Analytics', 'page' => 'Sidebar Navigation'],
            ['key' => 'sidebar.broadcasts', 'label' => 'Sidebar Broadcasts', 'page' => 'Sidebar Navigation'],
            ['key' => 'sidebar.users', 'label' => 'Sidebar Users', 'page' => 'Sidebar Navigation'],
            ['key' => 'sidebar.translations', 'label' => 'Sidebar Translations', 'page' => 'Sidebar Navigation'],
            ['key' => 'sidebar.settings', 'label' => 'Sidebar Settings', 'page' => 'Sidebar Navigation'],
            ['key' => 'landing.float.2fa', 'label' => 'Landing Floating Card: 2FA', 'page' => 'Landing Page'],
            ['key' => 'landing.float.email_verified', 'label' => 'Landing Floating Card: Email Verified', 'page' => 'Landing Page'],
            ['key' => 'landing.float.recovery_codes', 'label' => 'Landing Floating Card: Recovery Codes', 'page' => 'Landing Page'],
            ['key' => 'landing.feature.two_factor_auth', 'label' => 'Landing Feature: Two-Factor Auth', 'page' => 'Landing Page'],
            ['key' => 'landing.feature.email_verification', 'label' => 'Landing Feature: Email Verification', 'page' => 'Landing Page'],
            ['key' => 'landing.feature.admin_controls', 'label' => 'Landing Feature: Admin Controls', 'page' => 'Landing Page'],
            ['key' => 'landing.feature.dark_light_mode', 'label' => 'Landing Feature: Dark/Light Mode', 'page' => 'Landing Page'],
            ['key' => 'landing.feature.dynamic_fonts', 'label' => 'Landing Feature: Dynamic Fonts', 'page' => 'Landing Page'],
            ['key' => 'landing.feature.password_security', 'label' => 'Landing Feature: Password Security', 'page' => 'Landing Page'],
            ['key' => 'auth.social.google', 'label' => 'Auth Login: Google', 'page' => 'Auth Pages'],
            ['key' => 'auth.social.facebook', 'label' => 'Auth Login: Facebook', 'page' => 'Auth Pages'],
            ['key' => 'auth.social.twitter', 'label' => 'Auth Login: X/Twitter', 'page' => 'Auth Pages'],
            ['key' => 'auth.social.telegram', 'label' => 'Auth Login: Telegram', 'page' => 'Auth Pages'],
        ];
    }
}
