# PROJECT_STATE

## Completed

- [x] Laravel 12 + Jetstream (Livewire) base application setup
- [x] Email verification and secure auth flow
- [x] Two-Factor Authentication (TOTP) with recovery codes
- [x] Super admin middleware and protected admin settings route
- [x] Runtime SMTP loading from DB (`AppServiceProvider`)
- [x] SMTP admin UI with test email and anti-spam troubleshooting guidance
- [x] Terms of Service / Privacy Policy DB-backed pages
- [x] Branding controls (app name, logo, favicon, footer)
- [x] Theme designer + custom theme controls
- [x] Google Fonts API integration and font management UI
- [x] Landing page CMS sections (CRUD + visibility + order)
- [x] Full landing hero customization (badge, heading lines, subtitle, CTAs)
- [x] Landing section visibility toggles and visual effects toggles
- [x] Upload-based background system for Auth/App/Landing (image/video)
- [x] Documentation page route and view (`/documentation`)
- [x] Migration applied for latest settings columns (2026_02_28_090000...)

## In Progress

- [ ] Cross-page QA for background mode precedence and fallback behavior
- [ ] SMTP deliverability verification across providers (Gmail/custom domains)
- [ ] Final documentation polish and consistency checks

## Planned

- [ ] Security activity/audit logging
- [ ] Expanded role/permission model beyond super admin
- [ ] API token + scoped access capabilities
- [ ] Security notifications/alerts dashboard
- [ ] CI pipeline for tests, linting, and build validation

## Known Issues

- [ ] SMTP can be rejected with `550` spam classification if sender/domain records are not aligned (SPF/DKIM/DMARC, sender mismatch)
- [ ] Local Node.js version warning (`20.17.0` vs Vite recommended `20.19+`)
- [ ] Some editor diagnostics for Blade/Tailwind are noisy false positives on dynamic CSS/Blade expressions

## Next Task

- [ ] Perform a full manual QA pass of admin customization:
    - [ ] Upload image/video for Auth background and verify on login/register/forgot pages
    - [ ] Upload image/video for App background and verify on dashboard/settings pages
    - [ ] Upload image/video for Landing background and verify overlay/readability
    - [ ] Confirm landing hero text/CTA updates from admin settings are reflected live
    - [ ] Confirm features/CTA section visibility toggles and particle/floating-card toggles
- [ ] Validate SMTP with a real provider mailbox using "Send Test Email"
- [ ] Upgrade Node.js to >= 20.19 and re-run `npm run build`
