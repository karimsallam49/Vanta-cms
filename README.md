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
| **Administrator** | `admin@vantamoto.com` | `VantaAdmin2026` | All brands + users + showrooms + brands CRUD |
| **Data Entry** | `data@vantamoto.com` | `VantaEntry2026` | VOLTA + NOMAD only |

## Features

- **Auth** — session login, `throttle:5,1` rate limit, generic error messages,
  `hashed` password cast (bcrypt), CSRF on every form
- **Role-based authorization**
  - `admin` — everything
  - `data_entry` — CRUD on motorcycles/offers/test-rides **only for assigned
    brands**, enforced at three layers: list scope (`visibleTo`), policies
    (per-entity `brand_id` check), and `FormRequest::authorize()` (submitted
    `brand_id` must be in the user's assignment)
- **CRUD** — motorcycles (incl. paint colors JSON), offers, showrooms, brands,
  staff accounts with brand assignment
- **Test rides inbox** — status pipeline (`pending → contacted → confirmed →
  completed | cancelled`) + internal notes; customer submissions are never
  editable or deletable
- **Validation** — dedicated `FormRequest` per write action (hex-color regex,
  unique slugs, `Rule::enum`, date bounds, password strength rules)
- **Clean deletes** — controllers guard against deleting brands/showrooms
  that still have dependent records

## Architecture

```
app/
  Enums/            UserRole, TestRideStatus  (typed, with UI labels)
  Http/
    Controllers/    Auth/LoginController, Admin/* (thin — delegate to requests/policies)
    Middleware/     EnsureUserIsAdmin  (admin-only route group)
    Requests/       *Request           (validation + per-request authorization)
  Models/           relations + scopeVisibleTo() brand scoping
  Policies/         MotorcyclePolicy, OfferPolicy, TestRideRequestPolicy
resources/views/    layouts/app + per-resource index/form blades
database/migrations users+role, brands, user_brand, motorcycles, offers,
                    showrooms, test_ride_requests
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
