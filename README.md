# Filipino Club Membership and Dues System

A Laravel 12 REST API for the Filipino Club. The treasurer records monthly dues payments, officers manage the club, and members check their own payments. Every payment is split by the backend: each selected month is filled up to ฿30, and any extra becomes a donation.



**Student:** Mark Michael Morley | **Course:** IT 367 Web Application Development

## Live

- **API:** [https://filipino-club-api.onrender.com/api](https://filipino-club-api.onrender.com/api) (health check: [https://filipino-club-api.onrender.com/up](https://filipino-club-api.onrender.com/up))
- **Frontend:** *coming in week 3*

The API runs on a free Render instance, so the first request after a pause can take about a minute to wake up.

## Demo logins

| Role                 | Email                   | Password                             | Can do                                           |
| -------------------- | ----------------------- | ------------------------------------ | ------------------------------------------------ |
| Admin (Club Officer) | `admin@example.com`     | `PassApp` (live), `password` (local) | Manage announcements, view all payments and dues |
| Treasurer            | `treasurer@example.com` | `PassApp` (live), `password` (local) | Record payments, manage monthly dues             |
| Member               | `member@example.com`    | `PassApp` (live), `password` (local) | View own payments and dues                       |

These accounts hold fake data only. The live demo password is simple on purpose because this is a prototype.

## Documents

| File             | What it is                                                         |
| ---------------- | ------------------------------------------------------------------ |
| PRD.md           | Problem, roles, user stories, business rules, data model, API plan |
| API_ENDPOINTS.md | Full endpoint reference with request and response examples         |
| DEPLOYMENT.md    | Hosting, security controls, optimizations                          |
| DECISIONS.md     | Design decisions and why                                           |
| PROMPTS.md       | AI usage log, one entry per commit                                 |

## Setup (local)

Requirements: PHP 8.2 or newer (with `pdo_sqlite`, `mbstring`), Composer 2, SQLite (nothing to install).

bash
```bash
git clone <your-repo-url>
cd <your-repo>

composer install
cp .env.example .env
php artisan key:generate

# Set a demo password for the seeded accounts (one line in .env):
#   SEED_PASSWORD=password

touch database/database.sqlite        # Windows PowerShell: ni database/database.sqlite
php artisan migrate:fresh --seed

php artisan test
./vendor/bin/pint --test              # ./vendor/bin/pint fixes the style
php artisan serve                     # http://127.0.0.1:8000
```

Set `SEED_PASSWORD` before seeding. If it is empty, the demo accounts are created with an empty password and cannot log in.



Try it:

bash
```bash
curl -X POST http://127.0.0.1:8000/api/login \
  -H "Accept: application/json" -H "Content-Type: application/json" \
  -d '{"email":"treasurer@example.com","password":"password"}'
```

Send the returned token as `Authorization: Bearer <token>` on protected requests, with `Accept: application/json`.

## How a payment works

1. The treasurer creates a month of dues for a member (default ฿30).
2. The treasurer records a payment and chooses which months it covers.
3. The backend allocates the amount to each selected month, up to what it still owes. A month is **paid** once its allocations reach ฿30, and smaller payments add up.
4. Anything left over is saved as a donation and is never carried to the next month.
5. A payment, its allocations and its donation are saved in one transaction, so a rejected payment leaves nothing behind.

## ERD

ERD



Payments and monthly dues are linked many-to-many through `payment_monthly_dues`, which stores `allocated_amount`.

## Endpoints

Base path `/api`. 28 API routes (from `php artisan route:list --path=api`). Status: **Built** = implemented and tested.

| Method | URL                                 | Auth and role                                      | What it does                                                       | Status                                                                       |
| ------ | ----------------------------------- | -------------------------------------------------- | ------------------------------------------------------------------ | ---------------------------------------------------------------------------- |
| GET    | `/up` (no `/api`)                   | none                                               | Health check                                                       | Built                                                                        |
| POST   | `/api/register`                     | none, 6 per minute                                 | Create a Member account, returns user and token                    | Built                                                                        |
| POST   | `/api/login`                        | none, 6 per minute                                 | Returns user and token                                             | Built                                                                        |
| POST   | `/api/logout`                       | any logged-in user                                 | Revokes the current token                                          | Built                                                                        |
| GET    | `/api/user`                         | any logged-in user                                 | The current user                                                   | Built                                                                        |
| GET    | `/api/announcements`                | any logged-in user                                 | List. Members see published only                                   | Built                                                                        |
| POST   | `/api/announcements`                | admin                                              | Create                                                             | Built                                                                        |
| GET    | `/api/announcements/{announcement}` | any logged-in user (drafts: admin)                 | View one                                                           | Built                                                                        |
| PUT    | `/api/announcements/{announcement}` | admin                                              | Update                                                             | Built                                                                        |
| DELETE | `/api/announcements/{announcement}` | admin                                              | Delete                                                             | Built                                                                        |
| GET    | `/api/payments`                     | admin, treasurer                                   | List. Filters: `member_id`, `payment_method`, `from`, `to`, `sort` | Built                                                                        |
| POST   | `/api/payments`                     | treasurer                                          | Record a payment, split into allocations and a donation            | Built                                                                        |
| GET    | `/api/payments/{payment}`           | admin, treasurer, member (own)                     | View one                                                           | Built                                                                        |
| GET    | `/api/my/payments`                  | any logged-in user                                 | Own payments                                                       | Built                                                                        |
| GET    | `/api/monthly-dues`                 | admin, treasurer                                   | List. Filters: `month`, `year`, `member_id`, `status`              | Built                                                                        |
| POST   | `/api/monthly-dues`                 | treasurer                                          | Create a month for a member                                        | Built                                                                        |
| GET    | `/api/monthly-dues/{monthly_due}`   | admin, treasurer, member (own)                     | View one with its payments                                         | Built                                                                        |
| PUT    | `/api/monthly-dues/{monthly_due}`   | treasurer                                          | Change the required amount                                         | Built                                                                        |
| DELETE | `/api/monthly-dues/{monthly_due}`   | treasurer                                          | Delete (blocked if it has payments)                                | Built                                                                        |
| GET    | `/api/my/monthly-dues`              | any logged-in user                                 | Own dues and status                                                | Built                                                                        |
| GET    | `/api/members`                      | admin, treasurer                                   | List member profiles                                               | Built                                                                        |
| POST   | `/api/members`                      | admin (confirm treasurer in `MemberProfilePolicy`) | Create a member profile for an existing member user                | Built                                                                        |
| GET    | `/api/members/{member}`             | admin, treasurer, member (own profile)             | View one                                                           | Built                                                                        |
| PUT    | `/api/members/{member}`             | admin (confirm treasurer in `MemberProfilePolicy`) | Update                                                             | Built                                                                        |
| DELETE | `/api/members/{member}`             | admin                                              | Soft delete                                                        | Built                                                                        |
| GET    | `/api/members/{member}/payments`    | admin, treasurer, member (own)                     | Nested: a member's payments                                        | Built                                                                        |
| GET    | `/api/profile`                      | any logged-in user                                 | Own profile                                                        | Not built (a member views their own profile through `/api/members/{member}`) |
| PUT    | `/api/profile`                      | any logged-in user                                 | Update own name and phone                                          | Not built                                                                    |
| GET    | `/api/donations`                    | admin, treasurer                                   | List donations                                                     | Built                                                                        |
| GET    | `/api/reports/monthly`              | treasurer                                          | Payment, dues and donation totals for a month                      | Built                                                                        |
| GET    | `/api/reports/annual`               | treasurer                                          | Totals grouped by payment year                                     | Built                                                                        |

Payments cannot be edited or deleted. `PUT` and `DELETE` on a payment return 405.

## Business rules

| Rule | Behavior                                                                                                                |
| ---- | ----------------------------------------------------------------------------------------------------------------------- |
| BR01 | Only the treasurer can record payments (others get 403)                                                                 |
| BR02 | A payment needs an existing member, an amount above 0, a date and a method of `cash` or `bank_transfer` (otherwise 422) |
| BR03 | Each member owes at least ฿30 per month                                                                                 |
| BR04 | A month is paid once its allocations reach ฿30                                                                          |
| BR05 | Anything above what the selected months owe becomes a donation, never carried forward                                   |

## Level-up features

*Confirm each row against the code before submitting.*

| Feature                                            | Points | Code                                                           | Tests                                                                     |
| -------------------------------------------------- | ------ | -------------------------------------------------------------- | ------------------------------------------------------------------------- |
| Reports and charts                                 | 6      | `ReportController` (the chart is in the frontend, still to do) | `ReportApiTest` (4 tests)                                                 |
| Queued jobs or scheduled tasks (payment reminders) | 8      | *fill in the command, job and schedule file names*             | `MonthlyDuesReminderTest`, `SendMonthlyDuesReminderTest`                  |
| Frontend tests                                     | 6      | *fill in*                                                      | `resources/js/App.test.js` (check it covers login and one full CRUD flow) |

## Tests

`php artisan test` runs 96 tests covering authentication, announcements, members, donations, reports, monthly dues, payments, the payment service rules, the monthly reminder and the root route. CI runs Pint and the tests on every push.
