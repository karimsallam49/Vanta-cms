# VANTA MOTO — Database ERD

## Diagram

```mermaid
erDiagram
    users ||--o{ user_brand : "assigned"
    brands ||--o{ user_brand : "assigned to"
    brands ||--o{ motorcycles : "owns"
    brands ||--o{ offers : "scoped to"
    motorcycles ||--o{ test_ride_requests : "booked on"
    showrooms ||--o{ test_ride_requests : "hosts"

    users {
        bigint id PK
        string name
        string email UK
        string role "admin | data_entry"
        string password
    }

    user_brand {
        bigint user_id PK, FK
        bigint brand_id PK, FK
    }

    brands {
        bigint id PK
        string name
        string slug UK
        string segment "urban | adventure | performance | premium"
        string origin
        string tagline
        string accent "hex color"
    }

    motorcycles {
        bigint id PK
        bigint brand_id FK
        string name
        string slug UK
        int price
        int monthly_from
        int engine_cc
        int power_hp
        int weight_kg
        int top_speed_kmh
        string tagline
        string image
        json colors "[{name, hex}]"
    }

    offers {
        bigint id PK
        bigint brand_id FK "NULL = group-wide"
        string title
        text description
        string badge
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
```

## Design decisions

| Decision | Reason |
|---|---|
| `user_brand` composite PK pivot | Brand restriction is the core authorization rule — a proper join table keeps it queryable (`whereIn`) and prevents duplicate assignments. |
| `offers.brand_id` NULL-able | A `NULL` FK means "group-wide offer" — no sentinel row or extra table needed. Only admins may create these (enforced in `OfferRequest` + `OfferPolicy`). |
| `motorcycles.colors` JSON | Paint options are presentational attributes of a model, not entities users reference — a JSON column avoids a join table nobody would query. |
| `test_ride_requests` never deleted | Customer submissions are business records; staff may update `status`/`internal_note` only — the policy deliberately has no `delete`. |
| Indexes | `brand_id`, `status`, `status+created_at`, `country+city` — covering the exact list/filter queries the CMS runs. |
| `cascadeOnDelete` on FK children | Deleting a brand removes its motorcycles/assignments; guard clauses in controllers block destructive deletes when dependents exist. |

## Access model

```
admin       → canAccessBrand(any id) = true
data_entry  → canAccessBrand(id) = id ∈ user_brand

Motorcycle/Offer CRUD   → policy checks entity.brand_id
TestRideRequest update  → policy checks motorcycle.brand_id (via relation)
Showrooms / Brands / Users → `admin` middleware (group-level facilities)
```
