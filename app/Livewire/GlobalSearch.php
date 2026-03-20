<?php

namespace App\Livewire;

use App\Models\User;
use Livewire\Component;

class GlobalSearch extends Component
{
    public string $query = '';
    public bool $showResults = false;

    public function updatedQuery(): void
    {
        $this->showResults = strlen($this->query) >= 2;
    }

    public function getResultsProperty(): array
    {
        if (strlen($this->query) < 2) {
            return [];
        }

        $results = [];
        $q = mb_strtolower($this->query);
        $user = auth()->user();
        $isAdmin = $user?->isSuperAdmin();

        // 1. Search pages/navigation
        $pages = $this->getSearchablePages($isAdmin);
        foreach ($pages as $page) {
            if ($this->matchesQuery($q, $page['keywords'])) {
                $results[] = [
                    'type' => 'page',
                    'icon' => $page['icon'],
                    'title' => $page['title'],
                    'description' => $page['description'],
                    'url' => $page['url'],
                ];
            }
        }

        // 2. Search users (admin only)
        if ($isAdmin) {
            $users = User::where(function ($query) use ($q) {
                $query->where('name', 'like', "%{$q}%")
                      ->orWhere('email', 'like', "%{$q}%");
            })->limit(5)->get();

            foreach ($users as $u) {
                $results[] = [
                    'type' => 'user',
                    'icon' => 'user',
                    'title' => $u->name,
                    'description' => $u->email . ' · ' . str_replace('_', ' ', ucfirst($u->role)),
                    'url' => route('admin.users'),
                ];
            }
        }

        return array_slice($results, 0, 10);
    }

    private function matchesQuery(string $query, array $keywords): bool
    {
        foreach ($keywords as $keyword) {
            if (str_contains(mb_strtolower($keyword), $query)) {
                return true;
            }
        }
        return false;
    }

    private function getSearchablePages(bool $isAdmin): array
    {
        $pages = [
            [
                'title' => 'Dashboard',
                'description' => 'Overview, stats & quick actions',
                'url' => route('dashboard'),
                'icon' => 'dashboard',
                'keywords' => ['dashboard', 'home', 'overview', 'stats', 'quick actions'],
            ],
            [
                'title' => 'Profile',
                'description' => 'Update your profile info & password',
                'url' => route('profile.show'),
                'icon' => 'profile',
                'keywords' => ['profile', 'account', 'name', 'email', 'password', 'photo', 'avatar', 'two factor', '2fa', 'sessions', 'browser sessions', 'delete account'],
            ],
            [
                'title' => 'Documentation',
                'description' => 'View guides & technical docs',
                'url' => route('documentation'),
                'icon' => 'docs',
                'keywords' => ['documentation', 'docs', 'guide', 'help', 'manual'],
            ],
            [
                'title' => 'Terms of Service',
                'description' => 'View terms & conditions',
                'url' => route('terms.show'),
                'icon' => 'docs',
                'keywords' => ['terms', 'terms of service', 'conditions', 'legal'],
            ],
            [
                'title' => 'Privacy Policy',
                'description' => 'View privacy policy',
                'url' => route('policy.show'),
                'icon' => 'docs',
                'keywords' => ['privacy', 'privacy policy', 'data', 'cookies'],
            ],
        ];

        if ($isAdmin) {
            $adminPages = [
                [
                    'title' => 'User Management',
                    'description' => 'View, edit, verify & manage all users',
                    'url' => route('admin.users'),
                    'icon' => 'users',
                    'keywords' => ['users', 'user management', 'manage users', 'roles', 'verify', 'delete user'],
                ],
                [
                    'title' => 'Branding Settings',
                    'description' => 'App name, logo, favicon & backgrounds',
                    'url' => route('admin.settings', ['section' => 'branding']),
                    'icon' => 'settings',
                    'keywords' => ['branding', 'logo', 'favicon', 'app name', 'background', 'hero', 'cta'],
                ],
                [
                    'title' => 'Theme Designer',
                    'description' => 'Create & customize themes with gradients',
                    'url' => route('admin.settings', ['section' => 'themes']),
                    'icon' => 'settings',
                    'keywords' => ['theme designer', 'themes', 'gradient', 'blobs', 'create theme'],
                ],
                [
                    'title' => 'Default Theme',
                    'description' => 'Set default theme mode (dark/light/dim)',
                    'url' => route('admin.settings', ['section' => 'theme-mode']),
                    'icon' => 'settings',
                    'keywords' => ['default theme', 'dark mode', 'light mode', 'dim', 'theme mode'],
                ],
                [
                    'title' => 'Default Theme & Glass',
                    'description' => 'Glass blur, brightness, opacity & tint',
                    'url' => route('admin.settings', ['section' => 'theme-mode']),
                    'icon' => 'settings',
                    'keywords' => ['glass', 'glass effect', 'blur', 'frost', 'frosted', 'liquid', 'crystal', 'card', 'glass3d', 'tint', 'noise', 'saturation', 'brightness', 'opacity'],
                ],
                [
                    'title' => 'Custom Colors',
                    'description' => 'Primary, secondary, accent & status colors',
                    'url' => route('admin.settings', ['section' => 'custom-theme']),
                    'icon' => 'settings',
                    'keywords' => ['colors', 'custom colors', 'primary', 'secondary', 'accent', 'success', 'warning', 'danger', 'palette'],
                ],
                [
                    'title' => 'Font Settings',
                    'description' => 'Google Fonts selection & preview',
                    'url' => route('admin.settings', ['section' => 'fonts']),
                    'icon' => 'settings',
                    'keywords' => ['fonts', 'font', 'google fonts', 'typography', 'text'],
                ],
                [
                    'title' => 'Sidebar Settings',
                    'description' => 'Sidebar width, font size & icon size',
                    'url' => route('admin.settings', ['section' => 'sidebar']),
                    'icon' => 'settings',
                    'keywords' => ['sidebar', 'sidebar width', 'navigation', 'menu'],
                ],
                [
                    'title' => 'Auth Card Settings',
                    'description' => 'Login/register card styling & colors',
                    'url' => route('admin.settings', ['section' => 'auth-card']),
                    'icon' => 'settings',
                    'keywords' => ['auth card', 'login style', 'register style', 'auth design', 'card width', 'border radius'],
                ],
                [
                    'title' => 'Footer Settings',
                    'description' => 'Footer text, social links & visibility',
                    'url' => route('admin.settings', ['section' => 'footer']),
                    'icon' => 'settings',
                    'keywords' => ['footer', 'social links', 'copyright', 'sticky footer'],
                ],
                [
                    'title' => 'Landing Page Editor',
                    'description' => 'Manage landing page sections & content',
                    'url' => route('admin.settings', ['section' => 'landing']),
                    'icon' => 'settings',
                    'keywords' => ['landing page', 'landing', 'sections', 'hero', 'features'],
                ],
                [
                    'title' => 'Security Settings',
                    'description' => 'Email verification and access controls',
                    'url' => route('admin.settings', ['section' => 'security']),
                    'icon' => 'settings',
                    'keywords' => ['security', 'email verification', 'bypass', 'unverified login'],
                ],
                [
                    'title' => 'SMTP Settings',
                    'description' => 'Email server configuration and testing',
                    'url' => route('admin.settings', ['section' => 'smtp']),
                    'icon' => 'settings',
                    'keywords' => ['smtp', 'email', 'mail', 'encryption', 'host', 'port'],
                ],
                [
                    'title' => 'CAPTCHA Settings',
                    'description' => 'reCAPTCHA & Turnstile configuration',
                    'url' => route('admin.settings', ['section' => 'captcha']),
                    'icon' => 'settings',
                    'keywords' => ['captcha', 'recaptcha', 'turnstile', 'cloudflare', 'bot', 'spam'],
                ],
                [
                    'title' => 'Timezone Settings',
                    'description' => 'Set application timezone',
                    'url' => route('admin.settings', ['section' => 'timezone']),
                    'icon' => 'settings',
                    'keywords' => ['timezone', 'time', 'date', 'clock'],
                ],
                [
                    'title' => 'API Keys',
                    'description' => 'Google Fonts API key management',
                    'url' => route('admin.settings', ['section' => 'api']),
                    'icon' => 'settings',
                    'keywords' => ['api', 'api key', 'google fonts api', 'keys'],
                ],
            ];

            $pages = array_merge($pages, $adminPages);
        }

        return $pages;
    }

    public function navigate(string $url): void
    {
        $this->query = '';
        $this->showResults = false;
        $this->redirect($url, navigate: true);
    }

    public function render()
    {
        return view('livewire.global-search');
    }
}
