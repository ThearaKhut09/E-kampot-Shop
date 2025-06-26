<!-- Use this file to provide workspace-specific custom instructions to Copilot. For more details, visit https://code.visualstudio.com/docs/copilot/copilot-customization#_use-a-githubcopilotinstructionsmd-file -->

# E-Kampot Shop - Laravel E-commerce Platform

This is a comprehensive Laravel-based e-commerce platform with the following key features:

## Architecture & Technology Stack
- **Backend**: Laravel 12 with SQLite database
- **Frontend**: Blade templates with Tailwind CSS
- **Authentication**: Laravel Breeze with role-based access control
- **Packages**: Spatie Laravel Permission for roles, Laravel Sanctum for API auth

## Key Features
- Multi-role system (Admin/Customer)
- Comprehensive product categories (Phones, Electronics, Fashion, etc.)
- Shopping cart and checkout functionality
- Admin dashboard with full CRUD operations
- Dark mode support with user preference persistence
- Product reviews and ratings system
- Search and filtering capabilities
- Responsive design for all devices

## Database Schema
- Users (with roles)
- Products (with categories and images)
- Categories (hierarchical structure)
- Orders and Order Items
- Cart system
- Reviews and Ratings
- Site Settings

## Development Guidelines
- Follow Laravel best practices and conventions
- Use Eloquent relationships for data associations
- Implement proper validation for all forms
- Use middleware for authentication and authorization
- Optimize queries with eager loading
- Follow RESTful API conventions
- Maintain clean, readable code with proper documentation

## Security Considerations
- CSRF protection on all forms
- Input sanitization and validation
- Role-based access control
- Secure file uploads for product images
- XSS and SQL injection prevention
