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
- **Dark Mode** — Class-based, synced to DB per user (light / dark / auto)
- **Alumni Wall** — Social feed with posts, photos, @mentions, inline tag-all, reactions, comments, replies
  - Live polling (5 s) for new posts and comments, new-post banner, infinite scroll
  - Like / Dislike with reactor list (who liked / who disliked modal)
  - Post and comment editing with (Edited) indicator
  - Date filter: All Time / This Month / This Week
- **Wall Notifications** — Bell icon in navbar; notified on tag, comment, reply, like, dislike; click navigates to the post

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
| Realtime | 5-second AJAX polling (no WebSockets needed) |

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

## Wall Routes

| Method | Route | Purpose |
|---|---|---|
| GET | `/wall` | Feed with date filter + pagination |
| POST | `/wall` | Create new post (photo + @mentions + tags) |
| GET | `/wall/poll` | Polling endpoint — new posts, comments, notif count |
| GET | `/wall/post/{post}` | Navigate to specific post (from notification) |
| PATCH | `/posts/{post}` | Edit own post |
| DELETE | `/posts/{post}` | Delete own post (admin can delete any) |
| POST | `/posts/{post}/comments` | Add comment or reply |
| PATCH | `/comments/{comment}` | Edit own comment/reply |
| DELETE | `/comments/{comment}` | Delete comment (admin can delete any) |
| POST | `/posts/{post}/react` | Like / Dislike a post |
| POST | `/comments/{comment}/react` | Like / Dislike a comment |
| GET | `/posts/{post}/reactions` | List reactors (who liked / disliked) |
| GET | `/comments/{comment}/reactions` | List reactors for a comment |
| GET | `/wall/notifications` | Fetch notifications (marks all read) |
| POST | `/wall/notifications/read` | Mark all notifications read |
| POST | `/settings/theme` | Save theme preference to DB |

---

## Database Notes

- All foreign keys on the `alumni` table use `->references('id')->on('alumni')` — the table name is `alumni`, not `alumnis`
- Reactions use a polymorphic `morphMany` — shared between posts and comments
- Wall notifications are created on tag, comment, reply, like, and dislike events
- Address is stored as a combined string: `"Road, PO: PostOffice, Thana, Upazilla: Upazilla, District"` — parsed by regex in views
- Photo URL resolution: paths starting with `uploads/` use `asset()` directly; others use `asset('storage/'.$url)`

---

## License

Private project — for internal use by KZS SSC Batch 2002 reunion committee.
