# VANTA MOTO — CMS

> Fictional multi-brand motorcycle group CMS, built for a technical assessment.
> Companion backend for the **vanta-moto** React frontend. All data is fictional
> and for demonstration only — not affiliated with any real motorcycle brand.

## Stack

- **Laravel 12** (PHP 8.2)
- **SQLite** — zero-config database
- **Blade** + hand-rolled design system (`public/css/cms.css`) matching the
  public site's VANTA identity — no build step required

## Setup

```bash
composer install
cp .env.example .env        # DB_CONNECTION=sqlite already configured
php artisan key:generate
touch database/database.sqlite
php artisan migrate:fresh --seed
php artisan serve
```

Open http://127.0.0.1:8000 → redirects to `/admin` → login screen.

## Demo credentials

| Role | Email | Password | Scope |
|---|---|---|---|
| **Administrator** (custom role) | `admin@vantamoto.com` | `VantaAdmin2026` | Full access — bypasses all permission checks |
| **Data Entry** (custom role) | `data@vantamoto.com` | `VantaEntry2026` | VOLTA + NOMAD brands only |

> Credentials are development seed data — replace before any real deployment.

## Features

- **Auth** — session login, `throttle:5,1` rate limit, generic error messages,
  `hashed` password cast (bcrypt), CSRF on every form
- **Custom roles & permissions (RBAC)** — `roles` table + `Permissions`
  registry. Admins create roles with a per-module matrix
  (view/create/edit/delete/import/export for brands, motorcycles, offers,
  test rides, showrooms, site content, currencies, staff, roles, reports,
  activity). `perm:` middleware resolves the action from the route name;
  sidebar links hide what the role can't reach. Admin roles bypass checks;
  the last administrator role can't be demoted or deleted
- **Brand scoping** — non-admin roles manage content only for their assigned
  brands, enforced at three layers: list scope (`visibleTo`), policies
  (per-entity `brand_id` check), and `FormRequest::authorize()`
- **CRUD** — brands, motorcycles (bilingual, gallery with per-image colors,
  inventory, showroom assignment), offers (group/brand/model/test-ride
  scopes + discount %), showrooms (map pin, hours, test-ride open days),
  currencies, staff, roles
- **Bulk import/export** — Excel/CSV for brands & motorcycles with blank
  templates, preview-before-commit and row-level results
- **Test rides inbox** — status pipeline (`pending → contacted → confirmed →
  completed | cancelled`), per-request detail page with activity timeline,
  live pending badge in the sidebar (polled), automatic customer email on
  status change
- **Website content** — 17 editable page sections (hero, headings, worlds,
  service cards, test-ride CTA, footer) with bilingual fields + images,
  served through `/api/sections`
- **Reports** — KPIs, test-ride funnel/daily chart/by-showroom/by-brand,
  inventory & low stock, expiring offers, CSV export per section
- **Activity log** — append-only audit of logins, CRUD diffs (old → new per
  field), imports and exports; filterable by user/action/subject/date
- **Bilingual UI** — English/Arabic + RTL, stored per user
- **Public JSON API** — `/api/motorcycles`, `/api/showrooms`,
  `/api/offers`, `/api/sections`, `POST /api/test-rides` (slug/id
  resolution, stock check, open-day validation, throttled)
- **Validation** — dedicated `FormRequest` per write action
- **Clean deletes** — controllers guard against deleting records that still
  have dependents

## Architecture

```
app/
  Enums/            OfferScope, TestRideStatus  (typed, with UI labels)
  Support/          Permissions (RBAC registry), SiteContent (page-section registry)
  Http/
    Controllers/    Auth/LoginController, Admin/* (thin — delegate to requests/policies)
    Middleware/     EnsureUserIsAdmin, RequirePermission (perm:section.action)
    Requests/       *Request           (validation + per-request authorization)
  Models/           relations + scopeVisibleTo() brand scoping
  Policies/         MotorcyclePolicy, OfferPolicy, TestRideRequestPolicy
resources/views/    layouts/app + per-resource index/form blades
database/migrations users+role_id, roles, brands, user_brand, motorcycles,
                    motorcycle_images, offers, showrooms, currencies,
                    test_ride_requests, site_sections, activity_logs
docs/ERD.md         entity-relationship diagram + design rationale
```

See `docs/ERD.md` for the schema diagram and the reasoning behind the
authorization model.

## Assessment notes / assumptions

- Showrooms, brands and staff accounts are treated as *group-level* data —
  admin-only by design; brand content (models, offers, ride requests) is the
  data-entry surface. This demonstrates two distinct authorization tiers.
- Laravel 12 + Blade was chosen over a heavier SPA/Filament stack per the
  brief's "avoid over-engineering" — one login, focused CRUD, real policies.

## Attribution

- Login artwork — [Unsplash](https://unsplash.com) (free license).
- Framework/libraries — Laravel, Maatwebsite Excel (see `composer.json`).
- **AI use** — no AI-generated imagery; the codebase was developed with
  AI-assisted tooling (Devin), fully reviewed and understood by the candidate.
