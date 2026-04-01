# E-Kampot Shop - Laravel E-commerce Platform

E-Kampot Shop is a modern, full-featured e-commerce platform built with Laravel 12, featuring a comprehensive admin dashboard, user-friendly front-end with dark mode support, and complete shopping cart functionality.

## 🚀 Features

### Core Features

- **Multi-role system** (Admin/Customer) with Spatie Laravel Permission
- **Comprehensive product categories** (Phones, Electronics, Fashion, etc.)
- **Shopping cart and checkout** functionality
- **Product reviews and ratings** system
- **Dark mode support** with user preference persistence
- **Search and filtering** capabilities
- **Responsive design** for all devices
- **Admin dashboard** with full CRUD operations

### Technology Stack

- **Backend**: Laravel 12 with SQLite database
- **Frontend**: Blade templates with Tailwind CSS
- **Authentication**: Laravel Breeze with role-based access control
- **Packages**: Spatie Laravel Permission, Laravel Sanctum, Intervention Image

### Product Categories

- 📱 Phones (Smartphones, Feature phones, Accessories)
- 💻 Electronics (Laptops, Desktops, Tablets, etc.)
- 👗 Fashion (Clothing, Shoes, Accessories)
- 🏠 Home & Kitchen (Appliances, Cookware, Decor)
- 💄 Beauty & Personal Care
- ⚽ Sports & Outdoors
- 📚 Books & Stationery
- 🎮 Toys & Games
- 🏥 Health & Wellness
- 🚗 Automotive

## 📋 Requirements

- PHP 8.1 or higher
- Composer
- Node.js and NPM
- SQLite extension enabled

## 🛠️ Installation

1. **Clone the repository**

    ```bash
    git clone <repository-url>
    cd e-kampot-shop
    ```

2. **Install PHP dependencies**

    ```bash
    composer install
    ```

3. **Install Node.js dependencies**

    ```bash
    npm install
    ```

4. **Environment setup**

    ```bash
    cp .env.example .env
    php artisan key:generate
    ```

5. **Database setup**

    ```bash
    php artisan migrate --seed
    ```

6. **Build assets**

    ```bash
    npm run build
    ```

7. **Start the development server**
    ```bash
    php artisan serve
    ```

Visit `http://127.0.0.1:8000` to access the application.

## ☁️ Supabase Hosting Setup

To deploy this project with a free Supabase PostgreSQL database, follow:

- [SUPABASE_SETUP.md](SUPABASE_SETUP.md)

Quick local DB toggle:

```powershell
powershell -NoProfile -ExecutionPolicy Bypass -File scripts/switch-db.ps1 sqlite
powershell -NoProfile -ExecutionPolicy Bypass -File scripts/switch-db.ps1 supabase
```

## 👤 Default Users

After seeding, you can log in with these accounts:

### Admin Account

- **Email**: admin@ekampot.com
- **Password**: password
- **Role**: Administrator (Full access to admin panel)

### Customer Account

- **Email**: customer@example.com
- **Password**: password
- **Role**: Customer (Shopping access)

## 🎨 Admin Panel

The admin panel is accessible at `/admin` for users with admin role and includes:

- **Dashboard**: Overview of key metrics and analytics
- **Product Management**: Full CRUD operations for products
- **Category Management**: Hierarchical category system
- **Order Management**: View and update order statuses
- **User Management**: Manage user accounts and roles
- **Review Management**: Moderate and approve product reviews
- **Settings**: Site-wide configuration options

## 🛍️ Customer Features

### Public Pages

- **Homepage**: Featured products, categories, and promotional content
- **Product Catalog**: Browse with filtering and search
- **Product Details**: Detailed view with reviews and related products
- **Category Pages**: Browse products by category
- **About & Contact**: Company information and contact form

### Authenticated Features

- **Shopping Cart**: Add, update, and remove items
- **User Dashboard**: Order history and account management
- **Product Reviews**: Write and manage reviews
- **Wishlist**: Save products for later
- **Profile Management**: Update personal information

## 🌙 Dark Mode

The application supports system-wide dark mode that:

- Automatically detects user's system preference
- Allows manual toggle via navigation button
- Persists user preference in localStorage
- Applies across all pages and components

## 🎯 Key Components

### Models & Relationships

- **User**: With roles and permissions
- **Product**: With categories, reviews, and cart items
- **Category**: Hierarchical with parent-child relationships
- **Order & OrderItem**: Complete order management
- **Cart**: Persistent shopping cart
- **Review**: Product rating and review system
- **Setting**: Site configuration

### Security Features

- **CSRF Protection**: On all forms
- **Role-based Access Control**: Using Spatie Permission
- **Input Validation**: Comprehensive form validation
- **XSS Protection**: Sanitized outputs
- **Secure Authentication**: Laravel Breeze integration

## 🚀 Performance Optimizations

- **Eager Loading**: Optimized database queries
- **Image Optimization**: Using Intervention Image
- **Asset Compilation**: Optimized CSS/JS bundles
- **Database Indexing**: Proper index implementation
- **Caching Ready**: Built for Laravel cache integration

## 🧪 Testing

Run the test suite:

```bash
php artisan test
```

## 📁 Project Structure

```
├── app/
│   ├── Http/Controllers/
│   │   ├── Admin/           # Admin panel controllers
│   │   ├── HomeController.php
│   │   ├── ProductController.php
│   │   └── CartController.php
│   ├── Models/              # Eloquent models
│   └── Http/Middleware/     # Custom middleware
├── database/
│   ├── migrations/          # Database schema
│   └── seeders/            # Sample data
├── resources/
│   ├── views/              # Blade templates
│   ├── css/                # Tailwind CSS
│   └── js/                 # JavaScript assets
└── routes/
    └── web.php             # Application routes
```

## 🤝 Contributing

1. Fork the repository
2. Create a feature branch (`git checkout -b feature/amazing-feature`)
3. Commit your changes (`git commit -m 'Add some amazing feature'`)
4. Push to the branch (`git push origin feature/amazing-feature`)
5. Open a Pull Request

## 📄 License

This project is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).

## 📞 Support

For support, email info@ekampot.com or create an issue in the repository.

---

**E-Kampot Shop** - Your one-stop shop for everything you need! 🛒✨

## Laravel Sponsors

We would like to extend our thanks to the following sponsors for funding Laravel development. If you are interested in becoming a sponsor, please visit the [Laravel Partners program](https://partners.laravel.com).

### Premium Partners

- **[Vehikl](https://vehikl.com)**
- **[Tighten Co.](https://tighten.co)**
- **[Kirschbaum Development Group](https://kirschbaumdevelopment.com)**
- **[64 Robots](https://64robots.com)**
- **[Curotec](https://www.curotec.com/services/technologies/laravel)**
- **[DevSquad](https://devsquad.com/hire-laravel-developers)**
- **[Redberry](https://redberry.international/laravel-development)**
- **[Active Logic](https://activelogic.com)**

## Contributing

Thank you for considering contributing to the Laravel framework! The contribution guide can be found in the [Laravel documentation](https://laravel.com/docs/contributions).

## Code of Conduct

In order to ensure that the Laravel community is welcoming to all, please review and abide by the [Code of Conduct](https://laravel.com/docs/contributions#code-of-conduct).

## Security Vulnerabilities

If you discover a security vulnerability within Laravel, please send an e-mail to Taylor Otwell via [taylor@laravel.com](mailto:taylor@laravel.com). All security vulnerabilities will be promptly addressed.

## License

The Laravel framework is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).
