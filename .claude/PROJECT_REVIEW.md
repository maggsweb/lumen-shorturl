# Project Review — MaggsWeb ShortUrl API

_Reviewed: 2026-07-05 · Updated: 2026-07-05 · Branch: `2026-07-security-review` · Stack: Lumen 8 / PHP 8.2 / Basic Auth / MySQL_

A back-end URL-shortener API. Basic-Auth-protected endpoints create/delete short links,
list a user's links and activity, and delete an account; a public route handles redirects.

---

## ✅ Recently Fixed

- **🔴→✅ IDOR in `deleteLink` (P0).** `deleteLink` resolved the target link by short code
  without scoping to the owner, letting any authenticated user delete another user's link and
  its activity. Now chains `->byUser()` onto the lookup and returns **404** (was 500) when no
  owned link matches. Covered by regression tests `testUserCanDeleteOwnLink` and
  `testUserCannotDeleteAnotherUsersLink` (the previously-unused `$alt_user` fixture is now
  exercised). Commit `d5cb32a`. — `app/Http/Controllers/LinkController.php`
- **🟠→✅ Foreign `short_url` in `listActivity` (P1).** Lookup now scoped with `->byUser()` and
  returns 404 for a code the caller doesn't own. Regression tests `testCanFilterActivityByOwnLink`
  / `testCannotFilterActivityByAnotherUsersLink`. **Also fixed a latent bug found while testing:**
  `UserController` type-hinted `Laravel\Lumen\Http\Request` instead of `Illuminate\Http\Request`,
  so the injected request was empty and `$request->json('short_url')` was *always* null — the
  activity filter had never worked. — `app/Http/Controllers/UserController.php`
- **🟠→✅ Predictable / non-unique short codes (P1).** Generation now uses `Str::random(7)`
  (CSPRNG) instead of `base_convert(rand(), 10, 32)`; `shortCodeExists` checks `withTrashed()`;
  and a new migration adds a **unique index** on `links.short` as a backstop against the
  check-then-insert race. — `LinkController.php`, `2026_07_05_000000_add_unique_index_to_links_short.php`
- **🟠→✅ 404 on redirect miss (P1).** `RedirectController` returns 404 (was 500) for an unknown
  code. Regression test `testUnknownLinkReturns404`. — `app/Http/Controllers/RedirectController.php`
- **🟡→✅ Rate limiting (P2).** New `ThrottleRequests` middleware (cache-backed `RateLimiter`,
  per-user when authenticated / per-IP otherwise). `POST /create` = 30/min per user, redirect
  `GET /{link}` = 60/min per IP; returns 429 with `X-RateLimit-*` / `Retry-After` headers.
  Regression test `testCreateEndpointIsRateLimited`. — `app/Http/Middleware/ThrottleRequests.php`,
  `bootstrap/app.php`, `routes/web.php`
- **🟡→✅ `env()` → `config()` (P2).** `Link::getDomain()` now reads `config('app.url')` instead
  of `env('APP_URL')`, so it survives config caching. — `app/Models/Link.php`
- **🟡→✅ Error envelope (P2).** Error responses standardized to `{"error": "<message>"}` across
  all controllers and the 401 in `Authenticate` (previously bare arrays / plain text). Success
  payloads (links/activity) left unchanged by design. README documents the contract.

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
  plus authorization, 404, and rate-limit regression tests. 19 tests / 76 assertions passing.
- **Secrets handled correctly.** `.env` and logs are git-ignored and not tracked.

---

## ❌ What's Bad (bugs / security)

_(All former Medium items — foreign `short_url`, predictable/non-unique codes, the redirect
status code, and runtime `env()` — are now resolved; see **Recently Fixed**.)_

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
1. ~~Fix the `deleteLink` IDOR and scope link lookups to the authenticated user.~~ ✅ Done.
2. ~~Add a **unique index** on `links.short`; generate codes with `Str::random()`.~~ ✅ Done.
3. ~~Return correct status codes on the redirect miss (404).~~ ✅ Done — statuses now consistent.
4. ~~Add **rate limiting** on `/create` and the public redirect (abuse / enumeration).~~ ✅ Done.

**Robustness**
5. ~~Add negative-authorization tests — user A cannot delete/read user B's data.~~ ✅ Done for
   both `deleteLink` and `listActivity`.
6. Consider **cascade deletes** (DB-level `onDelete('cascade')`) as a backstop to the manual
   transactional deletes.
7. ~~Standardize the JSON error envelope instead of bare arrays like `['Link not found']`.~~
   ✅ Done — errors now `{"error": "<message>"}` (success payloads unchanged).

**Maintainability / DX**
8. ~~Replace `env()` calls with `config()` throughout.~~ ✅ Done (`Link::getDomain()`).
9. Add OpenAPI/Swagger docs and expand `README` with auth setup and error responses.
10. Add CI (GitHub Actions) to run tests + StyleCI on PRs.
11. Modernize the framework/toolchain per the "Needs Updating" section.

---

## Priority Snapshot

| Priority | Item | Status |
|----------|------|--------|
| 🔴 P0 | Fix `deleteLink` IDOR + add regression tests | ✅ Done (`d5cb32a`) |
| 🟠 P1 | Unique index + secure short-code generation | ✅ Done |
| 🟠 P1 | Reject foreign `short_url` in `listActivity` (+ Request type-hint bug) | ✅ Done |
| 🟠 P1 | 404 on redirect miss (`RedirectController`) | ✅ Done |
| 🟡 P2 | Rate limiting; `env()`→`config()`; error-envelope standardization | ✅ Done |
| 🟡 P3 | Framework/toolchain upgrade; CI; API docs | Open |
