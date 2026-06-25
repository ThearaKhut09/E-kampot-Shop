# Software Testing Plan — E‑Kampot Shop

## 1. Project Overview
E‑Kampot Shop is a Laravel 12 e‑commerce platform with Vite/Tailwind frontend, KHQR (Bakong) payments, and admin management. This testing plan adapts the provided sample to the E‑Kampot Shop project.

## 2. Objectives
- Verify core user flows (browse, search, product details, cart, checkout using KHQR).
- Validate admin workflows (product/category CRUD, order management, exports).
- Confirm integrations (Bakong KHQR, SMTP email, optional Groq AI chatbot).
- Ensure stability of background workers (payment polling, order finalization) and basic performance.

## 3. Scope
In scope:
- Functional testing for user and admin flows.
- Integration testing for payment (KHQR), email notifications, and search API.
- Regression tests for critical flows.
- Basic load checks for checkout path.

Out of scope:
- Full performance/stress testing at production scale.
- Third‑party shipping/carrier integrations (not implemented).
- Native mobile apps.

## 4. Test Types & Approach
- Functional — manual and automated (PHPUnit, HTTP tests) for controllers and services.
- Integration — end‑to‑end tests for KHQR flow and email delivery (staging keys required).
- UI / E2E — optional Playwright/Cypress tests for critical browser flows.
- Regression — run PHPUnit and Laravel Dusk/Playwright suites before releases.
- Basic performance — scripted requests for checkout path (small scale).

## 5. Test Environment
- Local dev: PHP 8.2, SQLite (quick smoke). Use `scripts/switch-db.ps1 sqlite`.
- Staging/Preprod: PostgreSQL (Supabase or hosted Postgres), queue backend (Redis recommended), SMTP test account, Bakong test credentials if available.
- Workers: run `php artisan queue:work` or supervisor-managed workers for payment polling.

## 6. Test Data
- Seeded demo products (varied categories, prices, images), seeded test users (customer, admin).
- Payment test accounts / sandbox or mocked bank responses for KHQR.
- Test emails set to dev inbox or mailtrap.

## 7. Test Cases (selected, runnable)
- TC-U-01: Browse products
  - Steps: Open homepage → click category → confirm product list loads and filters work.
  - Expected: Products displayed, pagination works, filters apply.

- TC-U-02: Search and Chatbot
  - Steps: Enter keyword in search → verify results; open AI assistant, ask for product recommendation.
  - Expected: Search returns matching products; chatbot calls search API and returns suggested items (no hallucinations).

- TC-U-03: Product detail and add to cart
  - Steps: Open product page → select variant/qty → add to cart → view cart.
  - Expected: Cart reflects selection and stock checks.

- TC-U-04: Checkout — KHQR success
  - Steps: Proceed to checkout → login/register → select KHQR payment → generate QR → simulate bank confirmation (or use test callback) → verify order finalized, stock decreased, confirmation email sent.
  - Expected: Order status becomes `paid` (or equivalent), receipt email sent, notification created.

- TC-U-05: Checkout — KHQR failure / timeout
  - Steps: Start checkout → generate QR → simulate timeout/no confirmation → verify order stays pending, user shown failure message, QR expired.
  - Expected: No stock reduction, order remains pending, user can retry.

- TC-A-01: Admin create product
  - Steps: Login as admin → Products → New product → add images, category, price → Save.
  - Expected: Product visible on frontend, images processed, SEO fields present.

- TC-A-02: Admin update order status
  - Steps: Login as admin → Orders → pick order → change status (shipped) → notify customer.
  - Expected: Order status updates, email/notification triggered.

- TC-S-01: Background worker (payment polling)
  - Steps: Start worker → create a pending KHQR order → simulate bank confirmation → observe worker picks up and finalizes.
  - Expected: Worker processes queue job, order finalized, events fired.

## 8. Pass/Fail Criteria
- Pass: All critical test cases (checkout, payment finalization, admin CRUD) pass with expected results.
- Blocker: Payment finalization, order creation, or major data-loss bug.
- Minor: UI misalignment, non-blocking email delays.

## 9. Risks & Mitigations
- External APIs (Bakong, SMTP) may be unstable — use sandbox/mocks for CI and contract tests for integration.
- AI chatbot may hallucinate — ensure it uses search results as source-of-truth and present sources.
- SQLite differences — test on Postgres staging before release.

## 10. Automation Recommendations
- Add PHPUnit feature tests for order lifecycle and payment polling logic.
- Add an E2E Playwright suite for critical flows (checkout, admin product CRUD).
- Use CI to run tests on every PR; mock external services or use dedicated test credentials.

## 11. Deliverables
- `docs/SOFTWARE_TESTING_E-KAMPOT_SHOP.md` (this document)
- Automated test suites under `tests/Feature` and `tests/Integration`
- Sample test data seeds and a `README.md` for running tests locally.

## 12. Next steps
- Confirm which environment (staging) has Bakong test creds; or provide mocks for payment in CI.
- Decide on E2E framework (Playwright or Laravel Dusk) and add pipeline steps.

---

*Prepared from your sample and adapted to the E‑Kampot Shop project.*
