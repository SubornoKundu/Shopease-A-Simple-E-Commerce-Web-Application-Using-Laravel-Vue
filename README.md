# ShopEase

A full-stack e-commerce web application built with **Laravel 13**, **Vue 3**, and **Inertia.js**

Customers can browse products by brand/category, search, manage a cart and wishlist, sign in with a
passwordless OTP flow, check out through a demo SSLCommerz-style payment page, and view their order
history.

> **Demo video:** → `https://youtu.be/2ZBkEn0rkEk?si=sV0kLgzU3-NWa4Wv`

---

## Tech stack

| Layer      | Choice                                                              |
|------------|----------------------------------------------------------------------|
| Backend    | Laravel 13 (PHP 8.3+), MySQL                                        |
| Frontend   | Vue 3 (`<script setup>`), Inertia.js 3, Tailwind CSS v4              |
| Auth       | OTP-over-email, backed by Laravel Sanctum                            |
| Payments   | A self-contained **demo** of an SSLCommerz-style checkout             |
| Build tool | Vite 8 (`laravel-vite-plugin`)                                       |
| Icons      | [`@lucide/vue`](https://lucide.dev)                                   |

## Feature checklist

- **Home page** — hero slider, brand list, category list, latest products, featured products
- **Products** — list + detail views, browse by brand, browse by category, search, sort
- **Auth** — OTP-based login/signup (no password), view profile, edit profile, logout
- **Cart** — add / update quantity / remove, running total
- **Wishlist** — add / remove
- **Checkout** — shipping details form → demo SSLCommerz payment page → order placed
- **Order history** — list of past orders + a detailed order view

---

## Getting started

### 1. Requirements

- PHP 8.3+ with the usual extensions (`pdo_mysql`, `mbstring`, `xml`, `curl`, `zip`)
- Composer
- Node.js 20+ and npm
- MySQL 8+ (or MariaDB)

### 2. Create an empty database

```sql
CREATE DATABASE shopease CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
```

`.env.example` already assumes a local MySQL server on `127.0.0.1:3306` with the `root` user and no
password (the common default for XAMPP/Laragon/Herd-style local setups) — if that matches your machine,
you won't need to edit anything in step 3.

### 3. Configure the environment

```bash
cp .env.example .env
```

If your MySQL credentials aren't the defaults, update these lines in `.env` before continuing:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=shopease
DB_USERNAME=root
DB_PASSWORD=
```

### 4. Install, generate a key, migrate, seed, and build

```bash
composer run setup
```

This one-liner (defined in `composer.json`) runs `composer install`, generates the app key, migrates
**and seeds** the database (5 brands, 6 categories, 25 products with images, 3 homepage sliders, and a
demo account — `demo@shopease.test` — with sample order history already in it), then installs npm
packages and builds the frontend.

Prefer to see each step run individually? — that's just:

```bash
composer install
php artisan key:generate
php artisan migrate --seed
npm install
npm run build
```

### 5. Link storage (optional)

Product/brand/category/slider images ship as static files under `public/images`, so this isn't required
for the seeded catalog — only needed if you later add real file uploads through `Storage::disk('public')`:

```bash
php artisan storage:link
```

### 6. Run it

```bash
composer run dev
```

This starts the Laravel server, a queue listener, live log tailing (`php artisan pail`), and the Vite dev
server together, all in one terminal. Visit **http://localhost:8000**.

Already built the frontend in step 4 and just want the server? `php artisan serve` alone is enough — you
only need `composer run dev` / `npm run dev` while actively editing frontend code.

---

## Logging in (there are no passwords)

1. Click **Login**, enter your name + any email address, and submit.
2. In `local`/demo config, the app shows the OTP directly on screen (see *"Why the OTP is shown on
   screen"* below) — enter it to sign in. First-time emails are registered automatically.
3. Or use the seeded demo account, **`demo@shopease.test`**, to see pre-existing order history.

## Design notes / trade-offs

A few decisions worth knowing about before you read the code or record the demo:

- **Why OTP is shown on screen.** There's no budget for a paid SMS/email gateway in this assignment, so
  `OTP_DEBUG_DISPLAY=true` (default, in `.env.example`) echoes the freshly generated code back to the
  login screen so the flow is fully demoable end to end. Every code is still hashed at rest
  (`App\Services\OtpService`) and every email is genuinely "sent" (via `MAIL_MAILER=log`, so it's also
  viewable in `storage/logs/laravel.log`). Set `OTP_DEBUG_DISPLAY=false` and configure real SMTP
  credentials to turn this into a normal production flow — nothing else about the auth code changes.
- **Why Sanctum *and* plain session auth.** ShopEase is a monolithic Inertia app — the Vue frontend is
  served by this same Laravel app, not a separately-hosted SPA — so the standard `web` session guard is
  what actually authenticates every request (see `Auth\OtpAuthController`). Laravel Sanctum is still
  installed and wired up (`HasApiTokens` on `User`, `personal_access_tokens` table, `config/sanctum.php`)
  so the same backend can issue bearer tokens for a future mobile app or third-party API client without
  further setup.
- **Why the SSLCommerz integration is a "demo".** SSLCommerz sandbox credentials aren't something this
  assignment has access to. `App\Services\SslCommerzDemoGateway` fakes exactly the two calls a real
  integration would make (`initiate()` and `complete()`) behind the same interface a real
  `sslcommerz/sslcommerz-php` integration would use, so swapping in real credentials later touches one
  file. The payment page (`Payment/Gateway.vue`) is clearly labeled **DEMO MODE**.
- **Why no Ziggy.** Ziggy is the usual way to get a `route()` helper on the JS side from Laravel's named
  routes. To keep the dependency list small, this project centralizes plain URL builders instead —
  see `resources/js/routes.js`.
- **Stock & cart lifecycle.** Stock is decremented — and the cart cleared — only once a (demo) payment
  actually succeeds, not at checkout submission. A pending order whose payment fails or is cancelled
  doesn't touch stock and leaves the cart untouched, so the customer can immediately retry.
- **Currency.** Prices are in BDT (৳), matching SSLCommerz's home market.

## Project structure

```
app/
  Http/Controllers/       Home, Product, Cart, Wishlist, Checkout, Order, Profile,
                           Auth/OtpAuthController, Payment/SslCommerzController
  Models/                 User, OtpVerification, Brand, Category, Product, Slider,
                           Wishlist, CartItem, Order, OrderItem
  Services/                OtpService, SslCommerzDemoGateway
  Mail/OtpMail.php
database/
  migrations/              all 10 app tables + Sanctum's personal_access_tokens
  seeders/                 brands, categories, products, sliders, a demo order history
resources/
  js/Pages/                one folder per feature (Products, Cart, Wishlist, Checkout,
                           Payment, Orders, Auth, Profile) + Home.vue
  js/Layouts/MainLayout.vue
  js/Components/           ProductCard, Pagination, FlashMessages
routes/web.php             every route, grouped by guest/auth
public/images/              seeded brand/category/product/slider artwork
```

---



## Submission checklist

Per the assignment brief:

- [ ] Push this repo to a **fresh** GitHub repository (this project's `.gitignore` already keeps
      `vendor/`, `node_modules/`, `.env`, and IDE folders out of git — nothing extra to strip out)
- [ ] Record the 3–3.5 minute walkthrough above
- [ ] Upload the video to Drive or YouTube and paste the link at the top of this README
- [ ] Submit the GitHub repo link (not a `.zip`)
