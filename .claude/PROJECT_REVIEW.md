# Project Review — MaggsWeb ShortUrl API

_Reviewed: 2026-07-05 · Branch: `master` · Stack: Lumen 8 / PHP 8.2 / Basic Auth / MySQL_

A back-end URL-shortener API. Basic-Auth-protected endpoints create/delete short links,
list a user's links and activity, and delete an account; a public route handles redirects.

---

## ✅ What's Good

- **Clean, consistent structure.** Conventional Lumen layout, StyleCI-enforced formatting,
  small focused controllers.
- **Good model design.** Query scopes (`byShortUrl`, `byUser`, `forUser`, `forLink`) keep
  query logic in the models, and `toArray()` projections cleanly control the API response shape.
- **Soft deletes + restore.** `createLink` restores a trashed link instead of duplicating it —
  a nice touch scoped correctly to the owning user.
- **Transactional deletes.** `deleteLink` / `deleteUser` wrap multi-table deletes in
  `DB::beginTransaction()` with rollback + error logging.
- **Activity/audit logging.** Create / Redirect / Error events are recorded with IP address.
- **Real test suite.** Factories, `DatabaseMigrations`, and coverage of the main happy paths
  (create, existing-link reuse, suggested code, redirect, auth success/failure, activity).
- **Secrets handled correctly.** `.env` and logs are git-ignored and not tracked.

---

## ❌ What's Bad (bugs / security)

### 🔴 High — Broken access control (IDOR) in `deleteLink`
`app/Http/Controllers/LinkController.php:75`
```php
$this->validate($request, ['short_url' => ['required', 'exists:links,short']]);
$link = Link::byShortUrl($short_url)->first();   // NOT scoped to the current user
```
`exists:links,short` matches a short code **anywhere** in the table, and `byShortUrl` isn't
scoped by `user_id`. Any authenticated user can delete **any other user's link and its
activity**. Contrast with `createLink`, which correctly chains `->byUser($currentUserId)`.
**Fix:** scope to owner — `Link::byShortUrl($short_url)->byUser($currentUserId)->first()` —
and return 404 when null.

### 🟠 Medium
- **Foreign `short_url` accepted in `listActivity`** (`UserController.php:66`). A short code
  belonging to another user passes `exists` validation; currently contained only because
  `forUser()` also filters `user_id`. Reject foreign short codes explicitly rather than relying
  on that.
- **Predictable / non-unique short codes** (`LinkController.php:117`). `base_convert(rand(),10,32)`
  is not cryptographically random and enumerable. Combined with the **missing unique index on
  `links.short`**, there is a check-then-insert race allowing collisions.
- **Wrong HTTP status codes.** "Not found" returns **500** (`RedirectController.php:30`,
  `LinkController.php:85`). Should be **404**; 500 implies a server fault and skews monitoring.
- **`env()` used at runtime** (`Link.php:103`, `getDomain()`). Returns null if config is ever
  cached. Use `config('app.url')`.

### 🟡 Low / polish
- **Dead validation + wrong docblock** in `listLinks` — validates `short_url` but never uses it;
  docblock says "List activity for a Link" (`UserController.php:26`).
- **Double query** — both list methods call `->count()` then `->get()`/`->paginate()`.
  `paginate()` alone covers both.
- **Confusing prefix check** — `strrpos($header,'Basic ') !== 0` (`AuthServiceProvider.php:40`).
  Use `str_starts_with(...)`.
- **`Activity::redirect`** dereferences `$link->user->id` (`Activity.php:89`); a soft-deleted
  owner makes `$link->user` null and throws. Use `$link->user_id`.

---

## 🔄 What Needs Updating

- **Unsupported framework.** `laravel/lumen-framework: ^8.3` (Lumen 8, ~2021, EOL) on PHP `^8.2`.
  No security patches; Lumen is maintenance-only. Plan a migration to Laravel (or a supported line).
- **`minimum-stability: dev`** in `composer.json` — prefer `stable` unless a dev dependency
  truly requires it.
- **PHPUnit 9** — modern is 10/11; low priority but part of the same modernization.
- **Stale working-tree artifacts** — old `storage/logs/*.log` and compiled
  `storage/framework/views/*.php` (not tracked, safe to clear).
- **`APP_DEBUG=true`** in `.env.example` — ensure production overrides to `false`.

---

## 🚀 How to Improve

**Security & correctness (do first)**
1. Fix the `deleteLink` IDOR and scope every link/activity lookup to the authenticated user.
2. Add a **unique index** on `links.short`; generate codes with `random_bytes()` / `Str::random()`.
3. Return correct status codes (404 not-found, 400/422 validation, 500 only for real faults).
4. Add **rate limiting** on `/create` and the public redirect (abuse / enumeration).

**Robustness**
5. Add negative-authorization tests — user A cannot delete/read user B's data. `$alt_user` is
   already created in `TestCase::setUp` but unused; it's set up for exactly this.
6. Consider **cascade deletes** (DB-level `onDelete('cascade')`) as a backstop to the manual
   transactional deletes.
7. Standardize the JSON error envelope (e.g. `{ "error": { "code", "message" } }`) instead of
   bare arrays like `['Link not found']`.

**Maintainability / DX**
8. Replace `env()` calls with `config()` throughout.
9. Add OpenAPI/Swagger docs and expand `README` with auth setup and error responses.
10. Add CI (GitHub Actions) to run tests + StyleCI on PRs.
11. Modernize the framework/toolchain per the "Needs Updating" section.

---

## Priority Snapshot

| Priority | Item |
|----------|------|
| 🔴 P0 | Fix `deleteLink` IDOR + add regression tests |
| 🟠 P1 | Unique index + secure short-code generation; correct status codes |
| 🟠 P1 | Reject foreign `short_url` in `listActivity` |
| 🟡 P2 | Rate limiting; `env()`→`config()`; error-envelope standardization |
| 🟡 P3 | Framework/toolchain upgrade; CI; API docs |
