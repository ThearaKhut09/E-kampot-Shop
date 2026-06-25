# E-Kampot Shop — Features

This document summarizes the implemented User and Admin features in the E-Kampot Shop codebase, with pointers to the relevant controllers and services.

## Purpose
Provide a concise feature list you can reuse in slides, thesis chapters, or documentation.

---

## User Features
- **Browse Products**: category listing, pagination, sorting, filters (price, rating, in-stock). See [app/Http/Controllers/ProductController.php](app/Http/Controllers/ProductController.php).
- **Product Details**: product page with related items and reviews. See [app/Http/Controllers/ProductController.php](app/Http/Controllers/ProductController.php).
- **Search**: keyword and filtered search integrated into product listing and used by the chatbot assistant. See [app/Http/Controllers/ProductController.php](app/Http/Controllers/ProductController.php) and [app/Http/Controllers/ChatbotController.php](app/Http/Controllers/ChatbotController.php).
- **Cart**: add, update, remove, clear items; cart totals and count; session & user support. See [app/Http/Controllers/CartController.php](app/Http/Controllers/CartController.php).
- **Checkout & Payment (KHQR)**: checkout flow that generates a KHQR QR code, creates a pending order, polls Bakong for payment status, and finalizes the order on confirmation. See [app/Http/Controllers/CheckoutController.php](app/Http/Controllers/CheckoutController.php) and [app/Services/BakongService.php](app/Services/BakongService.php).
- **Orders**: customers can view order list and details, and cancel pending orders. See [app/Http/Controllers/UserOrderController.php](app/Http/Controllers/UserOrderController.php).
- **Profile**: edit profile, update password, and delete account. See [app/Http/Controllers/ProfileController.php](app/Http/Controllers/ProfileController.php).
- **Reviews**: create, edit, delete reviews; product average rating updated automatically. See [app/Http/Controllers/ReviewController.php](app/Http/Controllers/ReviewController.php).
- **Notifications**: list, mark-as-read, mark-all-read, delete. See [app/Http/Controllers/NotificationController.php](app/Http/Controllers/NotificationController.php).
- **AI Chatbot Assistant**: UI widget plus backend product-matching and optional AI replies (uses `services.groq.api_key` when available). See [resources/views/components/chatbot-widget.blade.php](resources/views/components/chatbot-widget.blade.php) and [app/Http/Controllers/ChatbotController.php](app/Http/Controllers/ChatbotController.php).
- **Localization**: language switching is supported via routes. See [routes/web.php](routes/web.php).

---

## Admin Features
- **Product Management**: list, create, edit, delete products; image and gallery uploads; category assignment; featured flag. See [app/Http/Controllers/Admin/AdminProductController.php](app/Http/Controllers/Admin/AdminProductController.php).
- **Category Management**: create/edit/delete categories, parent/child relationships, image upload. See [app/Http/Controllers/Admin/AdminCategoryController.php](app/Http/Controllers/Admin/AdminCategoryController.php).
- **Order Management**: view orders, edit status and payment status, bulk updates, export. See [app/Http/Controllers/Admin/AdminOrderController.php](app/Http/Controllers/Admin/AdminOrderController.php).
- **User Management**: create/edit/delete users, assign roles, activate/deactivate, verify email, protections for last admin. See [app/Http/Controllers/Admin/AdminUserController.php](app/Http/Controllers/Admin/AdminUserController.php).
- **Settings**: site-wide settings (site name, currency, tax, shipping, logo/favicon). See [app/Http/Controllers/Admin/AdminSettingController.php](app/Http/Controllers/Admin/AdminSettingController.php).
- **Analytics & Reports**: revenue, orders, top products/categories, recent activity, CSV export. See [app/Http/Controllers/Admin/AdminAnalyticsController.php](app/Http/Controllers/Admin/AdminAnalyticsController.php).
- **System Tools**: cache clear, DB optimization, backups, maintenance mode toggles. See [app/Http/Controllers/Admin/AdminSystemController.php](app/Http/Controllers/Admin/AdminSystemController.php).
- **Bulk Actions**: bulk product/category/order/user operations and exports. See [app/Http/Controllers/Admin/AdminBulkController.php](app/Http/Controllers/Admin/AdminBulkController.php).
- **Notifications & Moderation**: admin notifications, review moderation endpoints. See admin controllers under `app/Http/Controllers/Admin`.

---

## Notes & Recommendations
- The KHQR payment flow is implemented end-to-end: QR generation, polling, confirmation and order finalization are present. Confirmed in `app/Services/BakongService.php` and `app/Http/Controllers/CheckoutController.php`.
- The chatbot provides strong product-matching logic; AI-enhanced replies are conditional on `services.groq.api_key`. Phrase slide text as "AI-powered chatbot assistant".
- If you want a slide-ready short version or a thesis-ready formatted feature table, tell me which format and I will add it to this same file or create a separate `FEATURES_SLIDE.md`.

---

Generated from a code scan on: 2026-06-01

