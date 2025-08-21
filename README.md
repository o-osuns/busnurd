# Laravel Product Module (Trial Task)

A tiny, production-lean CRUD app built with **Laravel 12**. Scope: email/password auth, create/list/view product, basic validation, CSRF protection, image upload, migrations/seeders. Timeboxed for evaluation, not production.

## ✨ Features
- Email/password authentication (Laravel Breeze + Blade/Tailwind)
- Product CRUD (name, price, image, description)
- Public list/view, authenticated create/update/delete
- Basic validation, CSRF, XSS-safe Blade templates
- Eloquent + Migrations + Seeder/Factory
- Postgres. However, I could have used SQLITE for this simple application
- Zero secrets in repo; `.env`-driven config

## 🧭 Architectural Notes
- **MVC + Actions**: Thin controllers delegating to `app/Actions/Product/*` for write operations. A Service pattern could also be used instead of Actions.
- **FormRequests** for validation.
- **Eloquent**
- **Blade+Tailwind** via Breeze for secure, accessible UI.
- **Images** stored in `storage/app/public/products` and served via `public/storage` symlink.


## 🚀 Quickstart (Local)

### Prerequisites
- PHP ≥ 8.3, Composer
- Node ≥ 18 (20+ recommended), npm
- Docker (Nginx, Postgres)
- Dbeaver (Optional) for database management

### 1) Clone & Install
```bash
git clone https://github.com/o-osuns/busnurd.git
cd busnurd
docker compose build --no-cache
docker compose up -d
cp .env.example .env
php artisan key:generate
```

# setup database connection

# in .env comment out the following and replace with the appropriate keys
# DB_HOST=db
# DB_PORT=5432
# DB_DATABASE=busnurd
# DB_USERNAME=postgres
# DB_PASSWORD=postgres

# Note
I intentionally used one branch (main) because this is an assessment. However, I used multiple commits
