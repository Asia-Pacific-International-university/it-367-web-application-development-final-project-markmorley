# API Endpoints: Filipino Club Membership and Dues System

Base URL: `https://YOUR-RENDER-ADDRESS.onrender.com` (local: `http://127.0.0.1:8000`)

All endpoints are under `/api` unless noted, return JSON, and use Laravel Sanctum bearer tokens.

```
Authorization: Bearer <token>
Accept: application/json
```

**Status key:** Built = implemented and tested. Not built = in the PRD but no route exists.

This list matches `php artisan route:list --path=api` (28 routes). Access for members, donations and reports comes from the tests. Confirm the cells marked *confirm* against the policy files.

## Roles

| Role | Can do |
| --- | --- |
| `admin` (Club Officer) | Manage members and announcements, view all payments and dues |
| `treasurer` | Record payments, manage monthly dues, view reports |
| `member` | View own profile, payments and dues, read published announcements |

## Common responses

| Status | Meaning |
| --- | --- |
| 200 / 201 / 204 | Success / created / deleted (no body) |
| 401 | No token or invalid token |
| 403 | Logged in but not allowed (wrong role or not your record) |
| 404 | Record does not exist |
| 405 | Method not supported (for example editing a payment) |
| 422 | Validation failed. Body has `message` and an `errors` object keyed by field |

Index endpoints are paginated, 10 per page, with `data`, `links` and `meta`. Use `?page=2`.

## 1. Authentication

| Method | Endpoint | Access | Purpose | Status |
| --- | --- | --- | --- | --- |
| GET | `/up` (no `/api`) | Public | Health check, returns 200 | Built |
| POST | `/api/register` | Public, 6 per minute per IP | Create a Member account, returns user and token (201) | Built |
| POST | `/api/login` | Public, 6 per minute per IP | Returns user and token. Wrong credentials return 422 | Built |
| POST | `/api/logout` | Any logged-in user | Revokes the current token | Built |
| GET | `/api/user` | Any logged-in user | Current user (`id`, `name`, `email`, `created_at`) | Built |

`POST /api/register` and `POST /api/login` body:

```json
{ "name": "Ana Cruz", "email": "ana@example.com", "password": "secret-pass", "password_confirmation": "secret-pass" }
```

Login needs only `email` and `password`. The role is always `member` on registration and is never read from the request.

## 2. Announcements (full CRUD)

| Method | Endpoint | Access | Purpose | Status |
| --- | --- | --- | --- | --- |
| GET | `/api/announcements` | Any logged-in user | List. Members see published only. Admin also sees drafts | Built |
| POST | `/api/announcements` | Admin | Create (201) | Built |
| GET | `/api/announcements/{announcement}` | Any logged-in user | View one. Drafts are admin only (403 otherwise) | Built |
| PUT | `/api/announcements/{announcement}` | Admin | Update. Send only the fields to change | Built |
| DELETE | `/api/announcements/{announcement}` | Admin | Delete (204) | Built |

Create body (`published_at` empty means draft):

```json
{ "title": "Foundation Day", "message": "Planning meeting on Friday.", "published_at": "2026-11-25 09:00:00" }
```

Response fields: `id`, `title`, `message`, `published_at`, `is_draft`, `author` (`id`, `name`), `created_at`, `updated_at`. The author is set from the logged-in admin and the author email is never returned.

## 3. Payments (create and read only)

Payments are never edited or deleted. `PUT` and `DELETE` return 405.

| Method | Endpoint | Access | Purpose | Status |
| --- | --- | --- | --- | --- |
| GET | `/api/payments` | Admin, Treasurer | List with filters and sort | Built |
| POST | `/api/payments` | Treasurer | Record a payment (201) | Built |
| GET | `/api/payments/{payment}` | Admin, Treasurer; Member for own only | View one | Built |
| GET | `/api/my/payments` | Any logged-in user | Own payments (empty list if no member profile) | Built |

List options: `member_id`, `payment_method` (`cash` or `bank_transfer`), `from` and `to` (`YYYY-MM-DD`), `sort` (`payment_date`, `-payment_date`, `amount`, `-amount`; default `-payment_date`), `page`.

Record a payment body:

```json
{
  "member_profile_id": 1,
  "amount": 50,
  "payment_date": "2026-11-25",
  "payment_method": "cash",
  "monthly_dues_ids": [3]
}
```

The backend splits the amount, never the client:

1. Each selected month is filled up to what it still owes (฿30 minus what was already allocated).
2. Anything left over is saved as a donation.
3. Everything is saved in one transaction, or nothing is.

Response (201):

```json
{
  "data": {
    "id": 12,
    "amount": "50.00",
    "payment_date": "2026-11-25",
    "payment_method": "cash",
    "member": { "id": 1, "full_name": "Demo Member" },
    "recorded_by": { "id": 2, "name": "Club Treasurer" },
    "monthly_dues": [{ "id": 3, "month": 11, "year": 2026, "allocated_amount": "30.00" }],
    "donation": { "amount": "20.00" },
    "created_at": "2026-11-25T08:00:00+00:00"
  }
}
```

`donation` is `null` when nothing was left over.

Validation (422): member must exist, `amount` above 0 with at most 2 decimals, valid date, method `cash` or `bank_transfer`, at least one month, every month must belong to that member, and a month that is already fully paid is rejected.

## 4. Members (full CRUD)

| Method | Endpoint | Access | Purpose | Status |
| --- | --- | --- | --- | --- |
| GET | `/api/members` | Admin, Treasurer | List. Options: `search` (name or student ID), `sort` (`full_name`, `created_at`, prefix `-` for descending), `page` | Built |
| POST | `/api/members` | Admin (*confirm* treasurer) | Create a member profile for an existing member user | Built |
| GET | `/api/members/{member}` | Admin, Treasurer; Member for own profile | View one | Built |
| PUT | `/api/members/{member}` | Admin (*confirm* treasurer) | Update | Built |
| DELETE | `/api/members/{member}` | Admin | Soft delete (payments are kept) | Built |
| GET | `/api/members/{member}/payments` | Admin, Treasurer; Member for own | Nested: one member's payments | Built |
| GET | `/api/profile` | Any logged-in user | Own profile | Not built (members use `/api/members/{member}` for their own profile) |
| PUT | `/api/profile` | Any logged-in user | Update own full name and phone (not student ID or role) | Not built |

## 5. Monthly dues (full CRUD)

| Method | Endpoint | Access | Purpose | Status |
| --- | --- | --- | --- | --- |
| GET | `/api/monthly-dues` | Admin, Treasurer | List with paid or unpaid status. Options: `month`, `year`, `status` (`paid` or `unpaid`), `member_id`, `page` | Built |
| POST | `/api/monthly-dues` | Treasurer | Create a month for a member. A repeated member, month and year returns 422 | Built |
| GET | `/api/monthly-dues/{monthly_due}` | Admin, Treasurer | View one with its payments and allocated amounts | Built |
| PUT | `/api/monthly-dues/{monthly_due}` | Treasurer | Change the required amount | Built |
| DELETE | `/api/monthly-dues/{monthly_due}` | Treasurer | Delete. Blocked with 422 if any payment is allocated to it | Built |
| GET | `/api/my/monthly-dues` | Any logged-in user | Own dues and paid status | Built |

A month is **paid** when its allocated amounts add up to 30 or more, and **unpaid** otherwise.

## 6. Donations and reports

| Method | Endpoint | Access | Purpose | Status |
| --- | --- | --- | --- | --- |
| GET | `/api/donations` | Admin, Treasurer | List donations | Built |
| GET | `/api/reports/monthly` | Treasurer | Dues collected, donations, paid and unpaid counts for a month | Built |
| GET | `/api/reports/annual` | Treasurer | The same totals for a year | Built |

## Business rules the API enforces

| Rule | Behavior |
| --- | --- |
| BR01 | Only the treasurer can record payments. Others get 403 |
| BR02 | A payment needs an existing member, an amount above 0, a date and a valid method. Otherwise 422 and nothing is saved |
| BR03 | Each member owes at least 30 per month |
| BR04 | A month is paid once its allocations reach 30. Smaller payments add up |
| BR05 | Anything above what the selected months owe becomes a donation and is not carried forward |

## Quick test with PowerShell

```powershell
$base = "https://YOUR-RENDER-ADDRESS.onrender.com"
$body = @{ email = 'treasurer@example.com'; password = 'YOUR_SEED_PASSWORD' } | ConvertTo-Json
$login = Invoke-RestMethod -Method Post -Uri "$base/api/login" -ContentType 'application/json' -Body $body
$h = @{ Authorization = "Bearer $($login.token)"; Accept = 'application/json' }
Invoke-RestMethod -Uri "$base/api/payments" -Headers $h
```
