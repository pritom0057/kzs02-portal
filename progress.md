# KZS 2002 Reunion Portal — Project Progress

## Overview
Laravel 12 alumni reunion portal for KZS SSC Batch 2002.
Live site: https://www.kzs02.com
Hosting: cPanel shared hosting (kzscom@s1301), DB: `kzscom_reunion`
Project path on server: `public_html/kzs-portal`

## Tech Stack
- **Backend**: Laravel 12, PHP 8.2
- **Frontend**: Blade templates, Tailwind CSS (CDN), Font Awesome
- **DB**: MySQL (cPanel)
- **Payment**: SSLCommerz gateway + manual (bKash/Nagad/bank transfer)
- **Auth**: OTP email verification
- **Dark mode**: `darkMode: 'class'` via localStorage

## Deployment
- Local zip: `/Users/pritom/WebstormProjects/kzs2k2/kzs-portal.zip`
- Build zip (from inside kzs-portal dir, excludes .env/vendor/node_modules):
  ```
  rm -f ../kzs-portal.zip && zip -r ../kzs-portal.zip . --exclude "*.git*" --exclude "node_modules/*" --exclude "vendor/*" --exclude ".env" --exclude ".env.*"
  ```
- Upload to `public_html/kzs-portal`, overwrite existing, then `php artisan migrate --force`

---

## Database — Key Tables

| Table | Purpose |
|-------|---------|
| `alumni` | Main user table (role: alumni/admin, status: pending/verified/rejected) |
| `event_registrations` | One per alumni — guests, t-shirt, payment info |
| `payment_transactions` | SSLCommerz gateway attempts only |
| `payment_logs` | Full audit log — all payment events (manual + SSL + admin actions) |
| `admin_notes` | Admin notes per alumni |
| `otp_verifications` | OTP codes for email login |

### `event_registrations` key columns
- `total_amount` — current calculated total (recalculates when registration updated)
- `paid_amount` — what admin has confirmed as paid (default 0)
- `payment_status` — unpaid / pending / paid
- `payment_method` — bkash / nagad / bank_transfer / null
- `payment_reference` — transaction ref submitted by alumni

### Pricing logic (`EventRegistration::calculateTotal`)
- Base: ৳2002 (covers member + spouse + all children)
- Extra guest: ৳1000 each
- Driver: ৳500
- Donation: added on top

### `payment_logs` type enum
- `submitted` — alumni submitted manual payment reference
- `confirmed` — admin confirmed payment
- `reset` — admin reset to unpaid
- `adjusted` — admin adjusted (guests/driver removed, credit acknowledged)
- `ssl_confirmed` — SSLCommerz payment confirmed by gateway
- `ssl_failed` — (reserved)

---

## Models & Relationships

```
Alumni
  hasOne  EventRegistration
  hasMany AdminNote
  hasMany PaymentTransaction   (SSLCommerz only)
  hasMany PaymentLog           (full audit trail)

EventRegistration
  belongsTo Alumni

PaymentLog
  belongsTo Alumni

PaymentTransaction
  belongsTo Alumni
```

---

## Controllers

### Auth
- `LoginController` — email/password login
- `RegisterController` — registration with OTP
- `OtpController` — OTP verify/resend

### Alumni (frontend)
- `ProfileController` — show/edit profile, photo upload
- `EventRegistrationController` — event registration form (save also syncs spouse_name to alumni profile)
- `DirectoryController` — alumni directory (grid/list view)

### Payment
- `PaymentController`
  - `confirm()` — show payment confirmation page
  - `initiate()` — start SSLCommerz session
  - `manual()` — alumni submits bKash/Nagad/bank ref → logs PaymentLog (submitted)
  - `success()` — SSLCommerz callback → sets paid_amount=total_amount, logs PaymentLog
  - `fail()` / `cancel()` — SSLCommerz failure handlers
  - `ipn()` — SSLCommerz server-to-server IPN (no auth/CSRF)

### Admin
- `DashboardController` — stats + pending alumni queue
- `AlumniController`
  - `index()` — list with status tabs + search
  - `show()` — detail page (loads eventRegistration, adminNotes, paymentLogs)
  - `verify()` / `reject()` — account status
  - `confirmPayment()` — sets paid=total, creates PaymentLog (confirmed)
  - `resetPayment()` — sets unpaid/0, creates PaymentLog (reset)
  - `adjustPayment()` — syncs paid down to new lower total, creates PaymentLog (adjusted)
  - `storeNote()` / `destroyNote()` — admin notes
- `RegistrationController` — paginated registrations list
- `TransactionController` — SSLCommerz transactions list (route exists, not in sidebar)
- `ExportController` — CSV exports for alumni and registrations

---

## Routes Summary

```
GET  /                          → welcome or dashboard redirect
GET  /register, POST /register
GET  /login, POST /login
GET  /verify-otp, POST /verify-otp, POST /verify-otp/resend
GET  /pending
POST /payment/ipn               → SSLCommerz IPN (no auth, no CSRF)

[auth + alumni.verified]
GET  /dashboard
GET  /profile, GET /profile/edit, POST /profile
GET  /event/register, POST /event/register
GET  /directory
GET  /payment/confirm
POST /payment/initiate
POST /payment/manual
POST /payment/success
POST /payment/fail
POST /payment/cancel

[auth + admin] prefix: /admin
GET  /admin/dashboard
GET  /admin/alumni
GET  /admin/alumni/{alumnus}
POST /admin/alumni/{alumnus}/verify
POST /admin/alumni/{alumnus}/reject
POST /admin/alumni/{alumnus}/notes
DELETE /admin/notes/{note}
POST /admin/alumni/{alumnus}/payment/confirm
POST /admin/alumni/{alumnus}/payment/reset
POST /admin/alumni/{alumnus}/payment/adjust
GET  /admin/registrations
GET  /admin/transactions        (no sidebar link)
GET  /admin/export/alumni
GET  /admin/export/registrations
```

---

## Views Structure

```
layouts/
  app.blade.php        — alumni frontend layout (dark mode, navbar with Admin Panel btn for admins)
  admin.blade.php      — admin sidebar layout
  auth.blade.php       — auth pages layout

welcome.blade.php
dashboard.blade.php    — cover.jpg hero, avatar, payment status card
profile/
  show.blade.php
  edit.blade.php       — back → dashboard
event/
  register.blade.php   — full registration form + payment section
    - Pre-fills address/spouse from saved data
    - Payment states: fully paid / pending / additional required / first time
    - Balance due shown in payment instructions
    - Kids t-shirt removed from children cards
directory/
  index.blade.php      — grid/list toggle, localStorage preference, mobile+email shown
payment/
  confirm.blade.php
  success.blade.php
  failed.blade.php
admin/
  dashboard.blade.php
  alumni/
    index.blade.php    — tabs: pending/verified/rejected/all + search
    show.blade.php     — profile, payment card (balance-aware buttons), payment history log, admin notes
  registrations/
    index.blade.php    — summary strip, filters, balance due column, confirm/adjust buttons
  transactions/
    index.blade.php    — SSLCommerz transactions list (accessible via /admin/transactions)
```

---

## Key Design Decisions

### Payment flow (manual)
1. Alumni submits bKash/Nagad/bank ref → `payment_status=pending`, PaymentLog(submitted) created
2. Admin sees "Confirm Additional Payment (৳X)" or "Confirm Payment" button
3. Admin confirms → `paid_amount=total_amount`, `payment_status=paid`, PaymentLog(confirmed) created
4. Admin can Reset → `paid_amount=0`, `payment_status=unpaid`, PaymentLog(reset)
5. If alumni adds guests (total increases), `paid_amount < total_amount` → alumni sees "Additional Payment Required" on registration page
6. If alumni removes guests (total decreases), admin uses "Adjust" → PaymentLog(adjusted)

### Payment flow (SSLCommerz)
1. Alumni clicks Online → `PaymentController::initiate()` → creates PaymentTransaction, redirects to gateway
2. SSLCommerz calls `success()` or `ipn()` → sets `paid_amount=total_amount`, PaymentLog(ssl_confirmed)

### Address storage
Stored as combined string: `"Road, PO: PostOffice, Thana, Upazila: Upazila, District"`
Parsed back in event registration view with `parseAddr()` regex function.

### Photo URL handling
```php
$photoSrc = str_starts_with($user->photo_url, 'uploads/')
    ? asset($user->photo_url)
    : asset('storage/' . $user->photo_url);
```

### Storage files (no symlink needed on cPanel)
Route: `GET /storage/{path}` → serves from `storage/app/public/`

---

## Admin Panel Features
- Dashboard: stats cards + pending alumni queue
- Alumni management: verify/reject, notes, payment confirm/reset/adjust
- Payment History per alumni: full audit log with icons (✓ confirmed, ⏳ submitted, ↺ reset, ⇅ adjusted)
- Registrations list: balance due shown, quick confirm/adjust buttons, pending count
- CSV exports: alumni list, registrations list
- "Admin Panel" button in alumni navbar for admin users

---

## Known Issues / Watch Points
- `payment_logs` foreign key must use `constrained('alumni')` not `constrained()` — table is `alumni` not `alumnis`
- Zip must be built from inside `kzs-portal/` dir to avoid nested folder on server
- Never include `.env` in zip — server has its own with real DB credentials
- After uploading new zip, always run `php artisan migrate --force`
- If migration fails with "table already exists", drop it in phpMyAdmin then re-run migrate
- `php artisan key:generate --force` needed if APP_KEY is missing/invalid on server
- SSLCommerz IPN route must be outside auth middleware and CSRF excluded
