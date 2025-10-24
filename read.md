# AB Sign Supplies Platform

This document captures the critical context for the AB Sign Supplies application so future maintainers can get productive quickly.

## Stack Overview
- Backend: PHP 8.2 (Laravel 12.34.0)
- Admin UI: Filament 3.3.43 with Filament Shield 3.9.x for role/permission management
- Frontend: Nuxt 4.1.3 (Vue 3.5) served from `frontend/`
- Database: MySQL 8.0 (development and production environments are provisioned on MySQL 8.x)
- Tooling: Node.js 18+ (Vite 7), Composer 2, npm

## Prerequisites
- PHP 8.2 with required extensions (`bcmath`, `ctype`, `fileinfo`, `json`, `mbstring`, `openssl`, `pdo_mysql`, `tokenizer`, `xml`)
- Composer 2.x
- Node.js 18 LTS or newer and npm
- MySQL 8.0 (or compatible MySQL 8.x instance)
- Redis optional but recommended for queues if you move beyond the default database queue driver

## Initial Setup
1. Clone the repository and `cd` into it.
2. Backend environment:
   - Copy `.env.example` to `.env` and adjust `APP_URL`, mail setup, and queue driver as needed.
   - Set database credentials (`DB_*`) that point to a MySQL 8.x database.
3. Frontend environment:
   - Copy `frontend/.env.example` (or create a new file) to `frontend/.env`. The default points to `http://127.0.0.1:8000/api`.
4. Install dependencies:
   - `composer install`
   - `npm install`
   - `npm install --prefix frontend`
5. Generate app key (only needed once): `php artisan key:generate`
6. Create a storage symlink (only once): `php artisan storage:link`
7. Run migrations and seeders: `php artisan migrate --seed`
   - Seeds create baseline company data, sample products, company services, contact messages, and Filament Shield roles with default users.

## Running the Project Locally
- Full stack development loop: `npm run dev:full` (starts Laravel at `127.0.0.1:8000` and the Nuxt dev server via `frontend/`)
- Run only the Laravel side: `php artisan serve`
- Run only the Nuxt frontend: `npm run dev --prefix frontend`
- Background workers (if needed):
  - Queue listener: `php artisan queue:listen`
  - Real-time log stream: `php artisan pail --timeout=0` (optional developer convenience)

## Testing & Quality
- Run the backend test suite: `php artisan test`
- Frontend type-checking and linting are currently manual; add tooling before enforcing CI rules.
- CI/CD: `.github/workflows/pull-requests.yml` runs formatting, tests, and build checks on pull requests.

## Default Access & Roles
- Seeder creates three Filament users with the default password `password`:
  - Super Admin: `admin@absigns.test`
  - Editor: `editor@absigns.test`
  - Viewer: `viewer@absigns.test`
- Filament Shield roles (`SuperAdmin`, `Editor`, `Viewer`) define access to resources, pages, and widgets. Adjust policies through `database/seeders/RolesAndPermissionsSeeder.php` and regenerate with `php artisan shield:generate` if models change.

## Key Feature Areas
- **Product Catalog**
  - CRUD via Filament (`app/Filament/Resources/ProductResource.php`)
  - API endpoints `GET /api/products`, `GET /api/products/{product}` support search, featured filters, and pagination (`app/Http/Controllers/Api/ProductController.php`)
  - Stats widgets (`app/Filament/Widgets/ProductStatsOverview.php`, `ProductsTrendChart.php`) surface catalog metrics on the admin dashboard.
- **Company Profile & Marketing Content**
  - Central branding, hero content, stats, and contact info managed through Filament (`app/Filament/Resources/CompanyInfoResource.php`)
  - Public API endpoint `GET /api/company` returns structured data (`app/Http/Resources/CompanyInfoResource.php`)
  - Nuxt home page consumes this data to render hero, stats, SEO structured data, and featured products (`frontend/app/pages/index.vue`)
- **Multi-Company Showcase**
  - `Company` and `CompanyService` models power the "Our Companies" pages (`frontend/app/pages/our-companies.vue`, `[slug].vue`)
  - API endpoints `GET /api/company-profiles` and `GET /api/company-profiles/{company}` feed the Nuxt layer.
- **Contact Pipeline**
  - Frontend contact form posts to `POST /api/contact` (`frontend/app/pages/contact.vue`)
  - Validation handled by `StoreContactMessageRequest` with records stored in `contact_messages`
  - Filament resource for reviewing submissions: `app/Filament/Resources/ContactMessageResource.php`
- **SEO Management**
  - `SeoEntry` model (see `app/Http/Controllers/Api/SeoController.php`) allows per-page metadata, Open Graph, and Twitter images.
  - Nuxt composables (`frontend/app/composables/useSeo.ts`) hydrate meta tags and JSON-LD structured data.
- **Authentication & Authorization**
  - Laravel Breeze handles auth scaffolding for Filament access (`routes/web.php`, `routes/auth.php`)
  - Filament Shield centralizes permission syncing and gate checks.

## Data & Storage Notes
- Uploaded assets (product images, hero media, logos) live on the `public` disk (`storage/app/public`). Keep `php artisan storage:link` active on each environment.
- Queues default to the `database` driver (see `.env`). Switch to Redis or another driver in production.
- Sessions and cache use the database drivers by default for durability.

## Deployment Considerations
- Ensure `APP_URL` matches the public domain for correct asset URLs in API responses.
- Configure a queue worker (Supervisor, Horizon, or similar) if you move email or other jobs off the request cycle.
- For Nuxt SSR/hosting, run `npm run build --prefix frontend` and serve the generated output (Nitro server or static hosting) pointing to the Laravel API base URL.
- Back up the MySQL database and the `storage/app/public` directory on a regular schedule.

## Useful Commands Cheat Sheet
- Refresh database from scratch: `php artisan migrate:fresh --seed`
- Sync Filament Shield permissions after code changes: `php artisan shield:generate`
- Clear caches after config/env updates:
  - `php artisan config:clear`
  - `php artisan cache:clear`
  - `php artisan route:clear`

Keep this document up to date as dependencies or workflows change so the next maintainer has an accurate reference.
