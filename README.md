# KZS 2002 Reunion Portal

A web portal for the **Kushtia Zilla School SSC Batch 2002** Silver Jubilee Reunion — built to handle alumni registration, event sign-up, family details, and payment collection.

Live site: **https://www.kzs02.com**

---

## Features

- **Alumni Registration & Verification** — Sign up with email OTP, admin approval workflow
- **Profile Management** — Name, photo, address, family info, professional details, social links
- **Event Registration** — T-shirt size, family/guest details, driver entry, donation — with live fee calculator
- **Bidirectional Data Sync** — Profile and event registration share spouse name, address, mobile, school info, emergency contact
- **Payment Collection**
  - Manual: bKash, Nagad, bank transfer (alumni submits transaction ID, admin confirms)
  - Online: SSLCommerz payment gateway
- **Admin Panel** — Verify/reject alumni, confirm/reset/adjust payments, add notes, full audit log, CSV exports
- **Alumni Directory** — Grid/list view with dark mode support
- **Dark Mode** — Class-based, persisted via localStorage

---

## Tech Stack

| Layer | Technology |
|---|---|
| Backend | Laravel 12, PHP 8.2 |
| Frontend | Blade templates, Tailwind CSS (CDN), Font Awesome |
| Database | MySQL (cPanel shared hosting) |
| Auth | Email OTP verification |
| Payment | SSLCommerz + manual (bKash / Nagad / Bank) |
| Hosting | cPanel shared hosting |

---

## Local Setup

```bash
git clone <repo-url>
cd kzs-portal

composer install

cp .env.example .env
php artisan key:generate
```

Edit `.env` with your database credentials, mail config, and SSLCommerz keys, then:

```bash
php artisan migrate
php artisan serve
```

> For file uploads (photos), the app uses a direct `/storage/{path}` route — no `storage:link` needed.

---

## Deployment (cPanel Shared Hosting)

1. Build the zip from inside the project directory:
```bash
rm -f ../kzs-portal.zip && zip -r ../kzs-portal.zip . \
  --exclude "*.git*" --exclude "node_modules/*" \
  --exclude "vendor/*" --exclude ".env" --exclude ".env.*"
```

2. Upload `kzs-portal.zip` to `public_html/kzs-portal` via cPanel File Manager and extract.

3. Run migrations:
```bash
php artisan migrate --force
```

> Never include `.env` in the zip — the server has its own with real credentials.

---

## Environment Variables

Key `.env` values needed:

```
APP_KEY=
APP_URL=https://www.kzs02.com

DB_DATABASE=
DB_USERNAME=
DB_PASSWORD=

MAIL_MAILER=smtp
MAIL_HOST=
MAIL_PORT=
MAIL_USERNAME=
MAIL_PASSWORD=

SSLCZ_STORE_ID=
SSLCZ_STORE_PASSWD=
SSLCZ_IS_SANDBOX=false
```

---

## Payment Flow

**Manual (bKash / Nagad / Bank Transfer)**
1. Alumni submits transaction reference → status set to `pending`
2. Admin reviews and confirms → status set to `paid`, `paid_amount` updated
3. Admin can reset or adjust if guests/amounts change

**SSLCommerz (Online)**
1. Alumni clicks Online → redirected to SSLCommerz gateway
2. On success, IPN callback updates `paid_amount` and logs `ssl_confirmed`

---

## Admin Routes

| Route | Purpose |
|---|---|
| `/admin/dashboard` | Stats + pending alumni queue |
| `/admin/alumni` | List with status tabs + search |
| `/admin/alumni/{id}` | Detail: profile, payment history, notes |
| `/admin/registrations` | All registrations with balance due |
| `/admin/export/alumni` | CSV export |
| `/admin/export/registrations` | CSV export |

---

## License

Private project — for internal use by KZS SSC Batch 2002 reunion committee.
