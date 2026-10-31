# VANTA MOTO — Database ERD

## Diagram

```mermaid
erDiagram
    roles ||--o{ users : "granted to"
    users ||--o{ user_brand : "assigned"
    brands ||--o{ user_brand : "assigned to"
    users ||--o{ activity_logs : "performs"
    brands ||--o{ motorcycles : "owns"
    currencies ||--o{ motorcycles : "prices"
    motorcycles ||--o{ motorcycle_images : "galleried by"
    motorcycles ||--o{ motorcycle_showroom : "stocked at"
    showrooms ||--o{ motorcycle_showroom : "stocks"
    brands ||--o{ offers : "scoped to"
    motorcycles ||--o{ offers : "scoped to"
    motorcycles ||--o{ test_ride_requests : "booked on"
    showrooms ||--o{ test_ride_requests : "hosts"

    roles {
        bigint id PK
        string name
        string slug UK
        string description
        bool is_admin "bypasses all permission checks"
        json permissions "['section.action', …]"
    }

    users {
        bigint id PK
        string name
        string email UK
        string password "bcrypt"
        string locale "en | ar"
        bigint role_id FK
    }

    user_brand {
        bigint user_id PK, FK
        bigint brand_id PK, FK
    }

    brands {
        bigint id PK
        string name
        string name_ar
        string slug UK
        string segment "urban | adventure | performance | premium"
        string origin
        string origin_ar
        string tagline
        string tagline_ar
        string accent "hex color"
    }

    motorcycles {
        bigint id PK
        bigint brand_id FK
        bigint currency_id FK "nullable"
        string name
        string name_ar
        string slug UK
        int price
        int monthly_from
        int engine_cc
        int power_hp
        int weight_kg
        int top_speed_kmh
        string tagline
        string tagline_ar
        text description
        text description_ar
        string image
        json colors "[{name, hex}]"
        bool is_active
        int quantity "units on floor"
    }

    motorcycle_images {
        bigint id PK
        bigint motorcycle_id FK
        string path "storage disk path"
        string color_name "nullable — links shot to paint swatch"
        string color_hex "nullable"
        int sort_order "drag-ordered"
    }

    motorcycle_showroom {
        bigint motorcycle_id PK, FK
        bigint showroom_id PK, FK
    }

    currencies {
        bigint id PK
        string code UK "USD | AED | SAR"
        string name
        string symbol
    }

    offers {
        bigint id PK
        bigint brand_id FK "NULL when scope = group"
        bigint motorcycle_id FK "NULL unless scope = model"
        string scope "group | brand | model | test_ride"
        string title
        text description
        string badge
        int discount_percent "nullable"
        date expires_on
        bool is_active
    }

    showrooms {
        bigint id PK
        string city
        string country
        string name
        string address
        string phone
        string hours
        json open_days "[1..7 weekday numbers]"
        decimal latitude
        decimal longitude
        bool is_service_center
    }

    test_ride_requests {
        bigint id PK
        bigint motorcycle_id FK
        bigint showroom_id FK
        string name
        string email
        string phone
        date preferred_date
        string status "pending | contacted | confirmed | completed | cancelled"
        text internal_note
    }

    site_sections {
        bigint id PK
        string key UK "hero | worlds | footer | …"
        json payload "{heading, heading_ar, image, …}"
    }

    activity_logs {
        bigint id PK
        bigint user_id FK "nullable — survives user deletion"
        string action "created | updated | deleted | login | logout | import | export"
        string subject_type "morph"
        bigint subject_id "morph"
        string description
        json properties "{changes: {field: [old, new]}}"
        string ip_address
        timestamp created_at "append-only, no updated_at"
    }
```

## Design decisions

| Decision | Reason |
|---|---|
| `roles` + `permissions` JSON | Custom RBAC — admins compose roles from a per-module action matrix without code changes. `is_admin` short-circuits every check; the last admin role can't be demoted or deleted. |
| `user_brand` composite PK pivot | Brand restriction is the second authorization axis — a proper join table keeps it queryable (`whereIn`) and prevents duplicate assignments. |
| `motorcycle_images` per-image colour fields | `color_name`/`color_hex` on the *image* (not the model) is what lets the public site swap the displayed shot when a paint swatch is picked. `sort_order` powers drag-ordering in the CMS. |
| `motorcycle_showroom` pivot | Stock is per showroom — test-ride booking validates the chosen model actually exists at the chosen showroom. |
| `showrooms.open_days` JSON | Weekdays the floor accepts test rides — the booking API rejects dates on closed days, and the public form blocks them upfront. |
| `offers.scope` + nullable FKs | One table models group-wide, brand, model and test-ride promotions without polymorphism — `scope` says which FK is meaningful. |
| `site_sections` key/payload | CMS-editable public copy without a schema per page — `SiteContent` registry defines the keys and their field shapes. |
| `activity_logs` morphs + `properties` | One audit table for every model; the diff payload (`{field: [old, new]}`) makes updates reviewable. Append-only — no `updated_at`, no deletes. |
| `currencies` | Real money data needs a code+symbol entity, not hardcoded strings; `nullOnDelete` keeps prices intact if a currency is removed. |
| `test_ride_requests` never deleted | Customer submissions are business records; staff may update `status`/`internal_note` only — the policy deliberately has no `delete`. |
| Indexes | `brand_id`, `status`, `status+created_at`, `country+city`, `action`, `subject`, `motorcycle_id+sort_order` — covering the exact list/filter queries the CMS runs. |
| `cascadeOnDelete` on children | Deleting a brand removes its motorcycles/assignments; controller guards block destructive deletes when dependents still exist. |

## Access model

```
role.is_admin          → every permission granted, brand scoping skipped
role.permissions       → 'section.action' strings checked by perm: middleware
                         + policies; sidebar hides unreachable sections
user_brand             → non-admin roles only see/manage their brands' content

Motorcycle/Offer CRUD   → policy checks entity.brand_id ∈ user's brands
TestRideRequest update  → policy checks motorcycle.brand_id (via relation)
Admin-only sections     → reports, activity log, currencies, roles, staff,
                          site content (default registry — configurable per role)
```
