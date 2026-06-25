# Test Results — E‑Kampot Shop

Latest local run: 2026-06-10

Local workflow was run in this environment using:

```powershell
php artisan migrate:fresh --seed
php artisan test
```

Result: 19 passed, 6 failed.

The raw output from the latest run is saved in `docs/test-run.txt`.

## How to run tests locally

1. Install dependencies and start workers if needed:

```powershell
composer install
npm install
scripts\switch-db.ps1 sqlite   # or `supabase` for staging
php 
php artisan queue:work --tries=1   # optional for worker tests
```

2. Run PHPUnit / Laravel tests:

```powershell
php artisan test
# or to produce JUnit XML for CI
vendor\bin\phpunit --log-junit storage\test-reports\junit.xml
```

3. Capture output and paste into the table below.

---

## Automated Test Summary

- Tests run: 25
- Passed: 19
- Failed: 6
- Skipped: 0
- Time: 2.88s

Latest run note: the same 19/6 result was reproduced after `php artisan migrate:fresh --seed` followed by `php artisan test`.


## Mapping: Checklist → Automated coverage & Result

| ID | Test | Automated? | Test file(s) | Result (Pass/Fail) | Notes |
|---|---|---:|---|---:|---|
| TC-01 | Login / Registration / Password flows | Yes | `tests/Feature/Auth/*` | Pass | Authentication, registration, password reset, confirmation tests all passed |
| TC-02 | Browse / Search / Filters / Pagination | Partially | `tests/Feature/ExampleTest.php` (smoke) | Fail | Homepage smoke test failed because `settings` table was missing in the first run; after `migrate:fresh --seed`, the home route still returned a failing response due to app-level settings access |
| TC-03 | Product page & Add to cart | No | - | - | Add feature tests for product/cart flows |
| TC-04 | Checkout — KHQR success | No (external dependency) | - | - | Use staging Bakong test creds or mock callbacks; add integration test `tests/Feature/OrderKHQRTest.php` |
| TC-05 | Checkout — KHQR failure / timeout | No | - | - | Simulate timeout via mock service or direct job dispatch in CI |
| TC-06 | Background worker: payment finalization | No | - | - | Add integration/worker tests, run with `php artisan queue:work` in staging |
| TC-07 | Admin: Product CRUD | No | - | - | Add admin feature tests (requires admin seed/user) |
| TC-08 | Admin: Order management | No | - | - |
| TC-09 | Emails & Notifications delivery | Partially | `tests/Feature/Auth/PasswordResetTest.php` | Pass | Password reset notification tests passed |
| TC-10 | Reviews & Ratings | No | - | - |
| TC-11 | User profile (edit, delete) | Yes | `tests/Feature/ProfileTest.php` | Fail | Profile routes returned `Access denied.` in the local workflow run; all 5 profile tests failed |
| TC-12 | Chatbot AI integration | No | - | - | Use mocked Groq responses in CI
| TC-13 | Storage & image processing | No | - | - | Add tests for image uploads via `Intervention` and storage assertions
| TC-14 | Performance (checkout smoke) | No (manual/scripted) | - | - | Use `ab` or k6 for small-scale smoke tests
| TC-15 | Security basics (CSRF/XSS/RBAC) | Partially | - | - | Add tests for RBAC (Spatie) and automated security checks

## White-box PHPUnit Results

This table lists the actual PHPUnit / Laravel test files from the latest local run.

| Test file | Type | Test count | Result | Notes |
|---|---|---:|---:|---|
| `tests/Unit/ExampleTest.php` | Unit | 1 | Pass | Basic unit smoke test passed |
| `tests/Feature/Auth/AuthenticationTest.php` | Feature | 4 | Pass | Login / logout flow passed |
| `tests/Feature/Auth/EmailVerificationTest.php` | Feature | 3 | Pass | Email verification flow passed |
| `tests/Feature/Auth/PasswordConfirmationTest.php` | Feature | 3 | Pass | Password confirmation flow passed |
| `tests/Feature/Auth/PasswordResetTest.php` | Feature | 4 | Pass | Reset link and password reset flow passed |
| `tests/Feature/Auth/PasswordUpdateTest.php` | Feature | 2 | Pass | Password update flow passed |
| `tests/Feature/Auth/RegistrationTest.php` | Feature | 2 | Pass | Registration flow passed |
| `tests/Feature/ExampleTest.php` | Feature | 1 | Fail | Home page returned `500` because `settings` table was missing in the test database |
| `tests/Feature/ProfileTest.php` | Feature | 5 | Fail | Profile routes returned `Access denied.` and failed all profile assertions |

**White-box total:** 25 tests total, 19 passed, 6 failed.

## Black-box Test Cases

These are user-facing browser tests for Playwright or Laravel Dusk. They test the website like a real customer or admin, without checking internal code.

| ID | Scenario | Tool | Steps | Expected Result |
|---|---|---|---|---|
| BB-01 | Login as customer | Playwright / Dusk | Open login page → enter valid email/password → submit | User reaches dashboard/home and is authenticated |
| BB-02 | Register new account | Playwright / Dusk | Open register page → fill form → submit | New user account is created and logged in |
| BB-03 | Browse products | Playwright / Dusk | Open homepage → open category → view products | Product list is visible with correct pagination and filters |
| BB-04 | Search products | Playwright / Dusk | Type keyword in search box → submit | Matching products are shown |
| BB-05 | Add item to cart | Playwright / Dusk | Open product page → choose quantity → click add to cart | Cart count updates and item appears in cart |
| BB-06 | Checkout with KHQR | Playwright / Dusk | Go to cart → checkout → choose KHQR → generate QR | QR appears and order is created as pending |
| BB-07 | KHQR success flow | Playwright / Dusk | Simulate payment confirmation/callback | Order becomes paid and user sees success message |
| BB-08 | KHQR failure or timeout | Playwright / Dusk | Do not confirm payment / let QR expire | Order stays pending and user can retry |
| BB-09 | Update profile | Playwright / Dusk | Open profile page → change name/email → save | Profile data updates successfully |
| BB-10 | Admin create product | Playwright / Dusk | Login as admin → open product create page → save new product | Product appears in admin list and frontend |

**Recommended black-box tool:** Playwright first, because it is already installed and works well for browser automation. Dusk is also suitable if you want a Laravel-only approach.

## Black-box Security Test (ZAP)

These ZAP exports were used as a black-box security pass against the running site.

| ID | Area | ZAP evidence | Result | Notes |
|---|---|---|---|---|
| ZAP-01 | Crawl coverage | `spider.csv` | Pass | ZAP discovered the main public routes: home, products, categories, auth pages, contact, cart, and common static assets |
| ZAP-02 | AJAX discovery | `AJAX spider.csv` | Pass | AJAX spider resolved additional assets and repeated homepage/product/category paths during browser-style crawling |
| ZAP-03 | Input fuzzing | `Active Scan.csv` | Needs review | ZAP sent payloads through `search`, `max_price`, `min_price`, and `sort` parameters, including sleep/XSL/command-injection patterns |
| ZAP-04 | Response handling | `Active Scan.csv` | Needs review | The application returned `200 OK` for many injected requests, so reflection, validation, and output encoding should be reviewed |
| ZAP-05 | External resources | `spider.csv`, `AJAX spider.csv` | Informational | Several third-party URLs were marked out of scope or returned `403`; this is expected for external CDNs and placeholder assets |

ZAP did not provide a clean export of explicit named vulnerabilities in the CSV files you shared, so the current evidence is best treated as a black-box security smoke test plus input-handling review, not a final vulnerability report.

## Actual pass/fail summary from local workflow

- Passed test files:
	- `tests/Unit/ExampleTest.php`
	- `tests/Feature/Auth/AuthenticationTest.php`
	- `tests/Feature/Auth/EmailVerificationTest.php`
	- `tests/Feature/Auth/PasswordConfirmationTest.php`
	- `tests/Feature/Auth/PasswordResetTest.php`
	- `tests/Feature/Auth/PasswordUpdateTest.php`
	- `tests/Feature/Auth/RegistrationTest.php`
- Failed test files:
	- `tests/Feature/ExampleTest.php`
	- `tests/Feature/ProfileTest.php`

## Main failure reasons

- `tests/Feature/ExampleTest.php`: `no such table: settings` during the first run; the table existed after `migrate:fresh --seed`, but the homepage request still failed due to app-level settings access.
- `tests/Feature/ProfileTest.php`: `Access denied.` returned by profile routes in the local test workflow, causing 5 profile-related assertions to fail.


## Paste raw PHPUnit output here

```
# Paste output from `php artisan test` here
```

## Example filled row (after running)

| TC-01 | Login / Registration / Password flows | Yes | `tests/Feature/Auth/*` | Pass | All auth tests passed (4 tests)

---

If you want, I can: 
- create the `tests/Feature/OrderKHQRTest.php` stub and other missing test stubs now, or
- generate a CSV results sheet to fill during manual testing.

Tell me which option you prefer and I will proceed.
