# Laor2FA

Security Laravel CMS app focused on secure source sharing with strong authentication and full branding control.

## Project Overview

- **Project Name:** Laor2FA
- **Project Type:** Security Laravel CMS App
- **Main Goal:** Share source code securely with Two-Factor Authentication (2FA)

Laor2FA provides a secure user flow (email verification + optional/forced 2FA), a customizable landing page CMS, and an admin control panel for SMTP, themes, fonts, legal pages, and visual branding.

## Tech Stack

### Backend

- PHP 8.2+
- Laravel 12
- Laravel Jetstream (Livewire stack)
- Laravel Fortify (authentication + 2FA)
- Laravel Sanctum
- Livewire 3

### Frontend

- Blade templates
- Tailwind CSS
- Vite
- GSAP + ScrollTrigger (landing/auth motion)

### Database

- MySQL (current environment)

### APIs

- Google Fonts API (admin font browser)

### Other Tools

- Composer
- NPM
- Cloudflare Tunnel (HTTPS exposure in development)

## Core Features

- Secure authentication flow with email verification and 2FA
- Admin-controlled branding/theme/background customization
- Dynamic landing page CMS with configurable sections and content

## Completed Features

- Laravel 12 + Jetstream Livewire authentication base
- Two-Factor Authentication (TOTP), recovery codes, and profile controls
- Super admin access control for admin settings
- Dynamic SMTP settings loaded from database at runtime
- Test email flow and SMTP troubleshooting guidance in admin panel
- Terms of Service and Privacy Policy pages backed by database content
- Branding manager (app name, logo, favicon, footer)
- Theme designer and custom theme controls (glass morphism style)
- Google Fonts API integration and font selection controls
- Landing page editor (CMS sections with ordering/visibility)
- Full landing hero customization (badge, 3 heading lines, subtitle, CTA buttons)
- Landing visual toggles (floating cards, particles)
- Uploaded background support for Auth pages, App pages, and Landing page (image/video)
- Public documentation page route and view (`/documentation`)

## Features In Progress

- End-to-end UI validation for new background upload flows across all pages
- SMTP deliverability hardening and provider-level anti-spam setup guidance
- Documentation refinement to keep dev + AI context fully synchronized

## Planned Features

- Activity/audit trail for security-critical actions
- Role expansion beyond super admin/user
- Optional API token management and scoped access controls
- Notification center for auth/security events
- Automated security checks and CI quality gates

## Known Issues

- SMTP may return `550` spam classification if sender/domain trust is not configured (SPF/DKIM/DMARC or mismatched From address)
- Local build warns on Node.js `20.17.0` (Vite recommends `20.19+`), though build currently completes
- Some IDE Blade/Tailwind diagnostics are false positives due to dynamic Blade/CSS expressions

## Installation

### 1) Clone and install dependencies

```bash
git clone <your-repo-url>
cd G2FASourceShare
composer install
npm install
```

### 2) Environment setup

```bash
cp .env.example .env
php artisan key:generate
```

Update `.env` for your database and base URL:

- `APP_URL`
- `DB_CONNECTION`
- `DB_HOST`
- `DB_PORT`
- `DB_DATABASE`
- `DB_USERNAME`
- `DB_PASSWORD`

### 3) Database + storage

```bash
php artisan migrate
php artisan storage:link
```

### 4) Build assets

```bash
npm run build
```

### 5) Run locally

```bash
php artisan serve
```

For full dev mode (server + queue + logs + vite), you can also run:

```bash
composer run dev
```

## Current Status

- Active development stage with major admin customization features implemented
- New landing/app/auth background upload and landing hero controls merged
- Documentation baseline in place (`README.md`, `AI_CONTEXT.md`, `PROJECT_STATE.md`)
- Next focus: QA sweep, deliverability reliability, and planning the next security module
