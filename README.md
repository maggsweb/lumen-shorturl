# MaggsWeb ShortUrl API

[![StyleCI](https://github.styleci.io/repos/319776559/shield?branch=master)](https://github.styleci.io/repos/319776559?branch=master)

A back-end URL-shortener API built with [Lumen 8](https://lumen.laravel.com/). It issues
short codes for long URLs, redirects visitors, and records per-link redirect activity.

- **Framework:** Lumen 8 (PHP 8.2+)
- **Auth:** HTTP Basic (per-user, credentials verified against a bcrypt hash)
- **Storage:** MySQL (SQLite in-memory for the test suite)
- **Client:** examples use `GuzzleHttp\Client`, but any HTTP client works

---

## Table of contents

- [Requirements](#requirements)
- [Installation](#installation)
- [Running the API](#running-the-api)
- [Authentication](#authentication)
- [Conventions](#conventions) — base URL, content type, rate limits, errors
- [Endpoints](#endpoints)
- [Resource shapes](#resource-shapes)
- [Testing](#testing)

---

## Requirements

- PHP **8.2+** with `ext-curl` and `ext-json`
- [Composer](https://getcomposer.org/)
- MySQL 5.7+ / 8.0 (or any driver supported by Lumen)

---

## Installation

```bash
# 1. Install dependencies
composer install

# 2. Create your environment file
cp .env.example .env

# 3. Set an application key (Lumen has no key:generate command — set it manually)
php -r "echo 'APP_KEY='.bin2hex(random_bytes(16)).PHP_EOL;"
# copy the output into APP_KEY= in your .env

# 4. Configure the database connection in .env (DB_DATABASE, DB_USERNAME, DB_PASSWORD)

# 5. Run migrations
php artisan migrate

# 6. (Optional) Seed sample data
#    Set SEED_USER_EMAIL and SEED_USER_PASSWORD in .env first — the seeder
#    reads the default user's credentials from the environment.
php artisan db:seed
```

> There is **no public registration endpoint**. Accounts are provisioned out of band
> (by seeding, or by inserting a row into `users` with a bcrypt-hashed password).

---

## Running the API

Lumen ships without an `artisan serve` command; use PHP's built-in server pointed at `public/`:

```bash
php -S localhost:8000 -t public
```

The API is then available at `http://localhost:8000`.

---

## Authentication

Every endpoint except the public redirect requires **HTTP Basic** authentication. Send the
base64-encoded `email:password` in the `Authorization` header:

```php
use GuzzleHttp\Client;

$client = new Client([
    'base_uri' => 'http://localhost:8000',
    'headers'  => [
        'Authorization' => 'Basic '.base64_encode('you@example.com:your-password'),
        'Content-Type'  => 'application/json',
        'Accept'        => 'application/json',
    ],
]);
```

A missing or invalid credential returns `401`:

```json
{ "error": "Unauthorized." }
```

---

## Conventions

**Base URL** — all paths are relative to your host, e.g. `http://localhost:8000`.

**Content type** — send request bodies as JSON with `Content-Type: application/json`.

**Rate limits** — exceeding a limit returns `429` with `X-RateLimit-Limit`,
`X-RateLimit-Remaining`, and `Retry-After` headers:

| Endpoint            | Limit                    | Keyed by |
|---------------------|--------------------------|----------|
| `POST /create`      | 30 requests / minute     | user     |
| `GET /{shortcode}`  | 60 requests / minute     | IP       |

**Errors** — error responses use a consistent envelope:

```json
{ "error": "Link not found" }
```

Validation failures return `422` with Lumen's default field-error shape:

```json
{ "long_url": ["The long url field is required."] }
```

| Status | Meaning                                   |
|--------|-------------------------------------------|
| `401`  | Missing / invalid Basic Auth credentials  |
| `404`  | Resource not found (or not owned by you)  |
| `422`  | Request validation failed                 |
| `429`  | Rate limit exceeded                       |
| `500`  | Unexpected server error                   |

---

## Endpoints

| Method   | Path          | Auth | Description                                   |
|----------|---------------|------|-----------------------------------------------|
| `POST`   | `/create`     | ✅   | Create (or return existing) short URL         |
| `DELETE` | `/link`       | ✅   | Delete a short URL and its activity           |
| `GET`    | `/links`      | ✅   | List the authenticated user's short URLs      |
| `GET`    | `/activity`   | ✅   | List the user's redirect activity             |
| `DELETE` | `/user`       | ✅   | Delete the account, its links, and activity   |
| `GET`    | `/{shortcode}`| —    | Public redirect to the original URL           |

---

### Create a short URL

`POST /create`

| Field       | Rules                                   | Notes                              |
|-------------|-----------------------------------------|------------------------------------|
| `long_url`  | required, valid URL, max 255 chars      | The destination URL                |
| `short_url` | optional, 5–20 chars, letters only      | Suggested code; a random, unique code is generated if omitted or taken |

If the `long_url` already exists for the user, the existing link is returned with `200`
(a soft-deleted match is restored). A new link returns `201`. Both responses wrap a single
[link object](#link) in an array.

```php
$response = $client->post('/create', [
    'body' => json_encode([
        'long_url'  => 'https://example.com/a/very/long/path',
        'short_url' => 'promo', // optional
    ]),
]);
```

```json
[
  {
    "short": "promo",
    "long": "https://example.com/a/very/long/path",
    "full": "http://localhost:8000/promo",
    "created": "2026-07-05T12:00:00.000000Z"
  }
]
```

Errors: `422` (validation), `429` (rate limit), `500` (`{"error":"Error creating new Link"}`).

---

### Delete a short URL

`DELETE /link`

| Field       | Rules                       |
|-------------|-----------------------------|
| `short_url` | required, must exist        |

The link must belong to the authenticated user; otherwise `404` is returned. Deleting a link
also removes its activity records (in a transaction).

```php
$response = $client->delete('/link', [
    'body' => json_encode(['short_url' => 'promo']),
]);
```

```json
["Link deleted"]
```

Errors: `404` (`{"error":"Link not found"}`), `422`, `500` (`{"error":"Error deleting Link"}`).

---

### List your short URLs

`GET /links`

Returns the authenticated user's links. Responses with more than 15 links are paginated
(a standard Lumen paginator object); otherwise a plain array of [link objects](#link) is returned.
When the user has no links, the body is the JSON string `"No Links found"`.

```php
$response = $client->get('/links');
```

```json
[
  { "short": "promo", "long": "https://example.com/...", "full": "http://localhost:8000/promo", "created": "2026-07-05T12:00:00.000000Z" }
]
```

---

### List your activity

`GET /activity`

Returns redirect activity for the authenticated user, most recent first. Optionally filter by
one of your own short codes. Responses with more than 15 records are paginated.

| Field       | Rules                         | Notes                                        |
|-------------|-------------------------------|----------------------------------------------|
| `short_url` | optional, must exist          | Must be a code you own, or `404` is returned |

```php
$response = $client->get('/activity', [
    'body' => json_encode(['short_url' => 'promo']), // optional filter
]);
```

```json
[
  {
    "action": "Redirect",
    "short": "promo",
    "long": "https://example.com/...",
    "created": "2026-07-05 12:34:56",
    "ip_address": "203.0.113.10"
  }
]
```

Errors: `404` (`{"error":"Link not found"}` — code not owned), `422`.

---

### Delete your account

`DELETE /user`

Deletes the authenticated user along with all of their links and activity (in a transaction).

```php
$response = $client->delete('/user');
```

```json
["User deleted"]
```

Errors: `500` (`{"error":"Error deleting User"}`).

---

### Redirect (public)

`GET /{shortcode}`

Public endpoint — no authentication. Records a `Redirect` activity entry and issues a `302`
redirect to the original URL. Unknown codes return `404`.

```
GET /promo  ->  302 Found, Location: https://example.com/a/very/long/path
```

Errors: `404` (`{"error":"Link not found"}`), `429` (rate limit).

---

## Resource shapes

### Link

| Field     | Type   | Description                                  |
|-----------|--------|----------------------------------------------|
| `short`   | string | The short code                               |
| `long`    | string | The original destination URL                 |
| `full`    | string | Fully-qualified short URL (`APP_URL/short`)  |
| `created` | string | ISO-8601 creation timestamp                  |

### Activity

| Field        | Type          | Description                                  |
|--------------|---------------|----------------------------------------------|
| `action`     | string        | `Create` or `Redirect`                       |
| `short`      | string / null | The related short code                       |
| `long`       | string / null | The related destination URL                  |
| `created`    | string        | Timestamp of the event                       |
| `ip_address` | string        | Client IP that triggered the event           |

---

## Testing

```bash
composer test
# or
vendor/bin/phpunit
```

Tests run against an in-memory SQLite database (configured in `phpunit.xml`) and cover
authentication, link creation, redirects, per-user authorization, 404 handling, and rate
limiting.
