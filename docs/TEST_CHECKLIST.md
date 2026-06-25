# E-Kampot Shop — Test Checklist

A concise checklist of critical tests to include in your presentation.

- TC-01: Login / Registration / Password flows (Functional, Critical)
- TC-02: Browse / Search / Filters / Pagination (Functional, High)
- TC-03: Product page & Add to cart (Functional, High)
- TC-04: Checkout — KHQR success (E2E/Critical, Critical)
- TC-05: Checkout — KHQR failure / timeout (E2E, High)
- TC-06: Background worker: payment finalization (Integration, Critical)
- TC-07: Admin: Product CRUD (Admin, High)
- TC-08: Admin: Order management (status, export) (Admin, High)
- TC-09: Emails & Notifications delivery (Integration, Medium)
- TC-10: Reviews & Ratings functionality (Functional, Medium)
- TC-11: User profile (edit, delete) (Functional, Medium)
- TC-12: Chatbot AI integration (search-backed) (Integration, Medium)
- TC-13: Storage & image processing (Integration, Medium)
- TC-14: Performance (checkout smoke) (Non-functional, Medium)
- TC-15: Security basics (CSRF/XSS/RBAC) (Non-functional, Critical)

Use `docs/TEST_CHECKLIST.csv` to track pass/fail during test runs.
