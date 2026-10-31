# Demo Credentials

Seeded accounts for the VANTA MOTO CMS — development/demo use only.
Replace before any real deployment.

**Login:** http://127.0.0.1:8000/login

| Name | Email | Password | Role | Scope |
|---|---|---|---|---|
| Group Admin | `admin@vantamoto.com` | `VantaAdmin2026` | Administrator | Full access — bypasses all permission checks |
| Sara Mansour | `data@vantamoto.com` | `VantaEntry2026` | Data Entry | VOLTA + NOMAD brands only — cannot see user/role management |

## Notes

- Passwords are hashed (bcrypt) in `database/database.sqlite`; the values above
  are the seed defaults from `DatabaseSeeder`.
- The Data Entry account demonstrates brand-scoped RBAC: it only manages
  motorcycles, offers and test rides for its assigned brands.
- The public website (`vanta-moto`) needs no login — it consumes the
  read-only API at `http://127.0.0.1:8000/api`.
