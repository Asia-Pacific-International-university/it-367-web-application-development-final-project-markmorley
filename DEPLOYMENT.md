# DEPLOYMENT.md: deployment, security and performance (CLO 3)

You will explain this file in the defense. Write the reasons yourself.

## 1. Live URLs

- **API base URL:** https://filipino-club-api.onrender.com
- **Health check:** https://filipino-club-api.onrender.com/up
- **CORS:** Local development origin `http://localhost:5173` is allowed.
- **Frontend:** Planned for Week 3; add its deployed URL once available.

## 2. Architecture

_Diagram of the deployed system (`docs/deployment.png`): browser, frontend host, API host, database, and any queue, cache or storage service._

| Part | Hosted on | Why this choice |
| --- | --- | --- |
| Frontend (Vue/React) | | |
| Laravel API | | |
| Database | | |

## 3. Environment variables

_Names only, never values._

| Variable | Purpose |
| --- | --- |
| `APP_KEY` | Encryption key |
| `DB_*` | Database connection |
| `CORS_ALLOWED_ORIGINS` | Frontend URL allowed to call the API |
| | |

## 4. Security controls

| Control | How it is applied | Why it matters for this app |
| --- | --- | --- |
| `APP_DEBUG=false`, `APP_ENV=production` | | |
| HTTPS only | | |
| CORS limited to the frontend | | |
| Rate limiting | | |
| Token expiry | | |
| No sensitive fields in responses | | |
| | | |

## 5. Performance optimizations

| Optimization | Where | Why |
| --- | --- | --- |
| Eager loading (no N+1) | | |
| Database indexes | | |
| Cache for _which read?_ | | |
| `php artisan optimize`, `composer install --no-dev -o` | | |
| Minified frontend build | | |

## 6. Performance Optimization Measurement

**Optimization:** Added `php artisan optimize` to the Docker startup command to cache Laravel's configuration, routes, events, and views.

**Endpoint tested:** `GET /api/payments` (authenticated)

**Method:** Sent one warm-up request, then measured four requests using `curl.exe` and averaged their total response times. The same endpoint and method were used before and after the optimization.

| Measurement | Average response time |
|---|---:|
| Before optimization | 305.2 ms |
| After optimization | 199.1 ms |
| Difference | 106.1 ms faster |
| Average response-time reduction | 34.8% |

The measured average response time decreased from 305.2 ms to 199.1 ms. This is a small sample, so network conditions and server load may affect the result; it is an observed measurement rather than a guarantee of performance.

## 7. Deployment steps

_The exact commands or settings you used, so someone else could redeploy it._
