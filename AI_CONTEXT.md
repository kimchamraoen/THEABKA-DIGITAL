# AI_CONTEXT

## Identity

- **Project:** Laor2FA
- **Type:** Security Laravel CMS App
- **Primary Goal:** Secure source-code sharing with strong authentication (2FA-first UX)

## Stack Snapshot

- **Backend:** PHP 8.2+, Laravel 12, Jetstream (Livewire), Fortify, Sanctum
- **Frontend:** Blade, Livewire 3, Tailwind CSS, Vite, GSAP
- **Database:** MySQL
- **APIs:** Google Fonts API
- **Infra Pattern:** Cloudflare Tunnel + forced HTTPS via `AppServiceProvider`

## High-Level Architecture

### Core Domains

- **Auth & Security:** Jetstream/Fortify auth, email verification, TOTP 2FA, recovery codes
- **Admin Control Plane:** Livewire admin modules for branding/theme/smtp/fonts/cms
- **Content Delivery:** Dynamic landing page + legal pages from DB-backed settings
- **Configuration Core:** Singleton `Setting` model used as global runtime config source

### Important Models

- `Setting` (singleton, cached): app identity, SMTP, legal, backgrounds, landing text/toggles
- `Theme`: named themes, gradients/colors, blobs, custom CSS
- `LandingSection`: CMS sections with visibility and sort order
- `User`: auth + 2FA state handled through Jetstream/Fortify conventions

### Important Modules

- `app/Livewire/Admin/BrandingSettings.php`
    - App branding, email texts, legal text
    - Auth/App/Landing background mode + uploads (image/video)
    - Landing hero and section-level toggles
- `app/Livewire/Admin/SmtpSettings.php`
    - SMTP credentials, force 2FA toggle, test email send
- `app/Livewire/Admin/LandingPageEditor.php`
    - CRUD for dynamic landing sections
- `app/Providers/AppServiceProvider.php`
    - `URL::forceScheme('https')` based on `APP_URL`
    - runtime SMTP config injection from `settings` table

## Request / View Flow

- Public routes:
    - `/` -> `landing` view
    - `/terms-of-service` and `/privacy-policy` -> DB content fallback to markdown
    - `/documentation` -> project usage/technical docs
- Auth routes:
    - Dashboard requires `auth + verified`
- Admin routes:
    - `/admin/settings` requires `auth + verified + SuperAdminMiddleware`

## Active Development Focus

- Validate new background upload UX end-to-end (auth/app/landing)
- Improve SMTP deliverability reliability and diagnostics
- Keep documentation synchronized with rapidly changing admin feature set

## Design & Implementation Rules

- Keep existing glass-morphism design language
- Use settings-driven UI behavior (avoid hardcoded branding content)
- Prefer DB-config runtime behavior over `.env` for admin-managed options
- Preserve backward compatibility when migrating settings fields (legacy fallbacks)
- Keep changes minimal and scoped; avoid unrelated refactors

## Known Constraints

- SMTP spam/rejection is often provider-side (SPF/DKIM/DMARC + sender mismatch)
- Vite warns on Node `20.17.0`; recommended `20.19+`
- IDE Blade/Tailwind diagnostics can report false positives on dynamic expressions

## Continuation Hints for AI

- Start with `Setting::instance()` for any global UI/config behavior
- Check `resources/views/layouts/app.blade.php` and `resources/views/layouts/guest.blade.php` for background rendering precedence
- Landing behavior is split between:
    - `resources/views/landing.blade.php` (hero/features/cta + animation)
    - `app/Livewire/Admin/LandingPageEditor.php` (dynamic CMS sections)
- Validate migrations before assuming columns exist
- After UI changes, run:
    - `php artisan migrate`
    - `php artisan view:cache` then `php artisan view:clear`
    - `npm run build`
