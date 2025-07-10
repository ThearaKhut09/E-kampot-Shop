# E-Kampot Shop - Laravel 12 Deployment Guide for Render

This guide provides step-by-step instructions for deploying your Laravel 12 e-commerce platform to Render with SQLite database.

## Prerequisites

1. **GitHub Account**: Your code must be in a GitHub repository
2. **Render Account**: Sign up at [render.com](https://render.com)
3. **Domain (Optional)**: For custom domain setup

## Deployment Options

### Option 1: Docker-based Deployment (Recommended)

This option uses Docker for a more reliable and consistent deployment.

#### Steps:

1. **Push your code to GitHub** with all the deployment files created
2. **Go to Render Dashboard** and click "New +"
3. **Select "Web Service"**
4. **Connect your GitHub repository**
5. **Configure the service:**
   - **Name**: `e-kampot-shop`
   - **Environment**: `Docker`
   - **Region**: `Oregon` (or your preferred region)
   - **Branch**: `main` (or your default branch)
   - **Root Directory**: Leave empty
   - **Dockerfile Path**: `./Dockerfile`

6. **Environment Variables** (Add these in Render dashboard):
   ```
   APP_NAME=E-Kampot Shop
   APP_ENV=production
   APP_DEBUG=false
   APP_URL=https://your-app-name.onrender.com
   APP_KEY=base64:YOUR_GENERATED_KEY
   LOG_CHANNEL=errorlog
   LOG_LEVEL=info
   DB_CONNECTION=sqlite
   SESSION_DRIVER=file
   QUEUE_CONNECTION=sync
   CACHE_DRIVER=file
   FILESYSTEM_DISK=local
   BROADCAST_DRIVER=log
   MAIL_MAILER=smtp
   VITE_APP_NAME=E-Kampot Shop
   ```

7. **Deploy**: Click "Create Web Service"

### Option 2: Native Build (Alternative)

If Docker deployment doesn't work, try this native approach.

#### Steps:

1. **Use the simplified render.yaml** (rename `render-docker.yaml` to `render.yaml`)
2. **In Render Dashboard:**
   - **Environment**: `Node`
   - **Build Command**: `./render-build.sh`
   - **Start Command**: `php artisan serve --host=0.0.0.0 --port=$PORT`

## Important Configuration Notes

### 1. SQLite Database
- The database file is automatically created during deployment
- It's stored in `/database/database.sqlite`
- Data persists between deployments on the same service

### 2. File Storage
- Use Render's disk storage for file uploads
- Files are stored in `/storage/app/public`
- The storage link is automatically created

### 3. Environment Variables
- **APP_KEY**: Will be auto-generated if not set
- **APP_URL**: Update this to your Render app URL
- **Mail Settings**: Configure if you need email functionality

### 4. Performance Optimization
- All caching is enabled (config, routes, views)
- Composer autoloader is optimized
- Frontend assets are built for production

## Post-Deployment Setup

### 1. Database Seeding (Optional)
If you want to seed your database with initial data:

1. **Enable seeding in build script**:
   - Uncomment the line in `build.sh`: `# php artisan db:seed --force`

2. **Create seeders for your e-commerce data**:
   ```bash
   php artisan make:seeder CategorySeeder
   php artisan make:seeder ProductSeeder
   php artisan make:seeder UserSeeder
   ```

### 2. Admin Account Setup
After deployment, create an admin account:

1. **Via Seeder** (Recommended):
   ```php
   // In DatabaseSeeder.php
   User::create([
       'name' => 'Admin',
       'email' => 'admin@ekampot.com',
       'password' => Hash::make('secure_password'),
       'email_verified_at' => now(),
   ])->assignRole('admin');
   ```

2. **Via Tinker** (if you have shell access):
   ```php
   php artisan tinker
   $user = User::create(['name' => 'Admin', 'email' => 'admin@example.com', 'password' => Hash::make('password')]);
   $user->assignRole('admin');
   ```

### 3. File Upload Configuration
For product images and other uploads:

1. **Storage Link**: Automatically created during deployment
2. **File Permissions**: Set properly in the build script
3. **Upload Limits**: Configure in your Laravel app

## Domain Setup

### Custom Domain
1. **In Render Dashboard**: Go to Settings → Custom Domains
2. **Add your domain**: `yourdomain.com`
3. **Update DNS**: Point your domain to Render's IP
4. **Update APP_URL**: Set to your custom domain

### SSL Certificate
- Automatically provided by Render
- No additional configuration needed

## Monitoring and Maintenance

### 1. Logs
- **Application Logs**: Available in Render dashboard
- **Error Tracking**: Configure services like Sentry (optional)

### 2. Database Backups
- **Important**: SQLite files can be lost during deployments
- **Recommendation**: 
  - Set up automated backups
  - Consider upgrading to PostgreSQL for production

### 3. Performance Monitoring
- **Render Metrics**: Built-in monitoring
- **Laravel Telescope**: Can be installed for detailed debugging

## Troubleshooting

### Common Issues:

1. **Build Failures**:
   - Check build logs in Render dashboard
   - Ensure all dependencies are in `composer.json`
   - Verify Node.js version compatibility

2. **Database Issues**:
   - Ensure SQLite is properly configured
   - Check file permissions
   - Verify migrations are running

3. **Asset Loading Problems**:
   - Check if `npm run build` completed successfully
   - Verify Vite configuration
   - Ensure `APP_URL` is set correctly

4. **Permission Errors**:
   - Storage and cache directories are set to 755
   - Database file has proper permissions

### Getting Help:
- Check Render documentation
- Review Laravel deployment guides
- Check application logs for specific errors

## Security Considerations

1. **Environment Variables**: Never commit `.env` files
2. **APP_KEY**: Use a strong, unique key
3. **Database**: SQLite is suitable for small to medium applications
4. **HTTPS**: Automatically enabled by Render
5. **Input Validation**: Ensure all forms have proper validation

## Scaling Considerations

For high-traffic applications, consider:
1. **Database**: Upgrade to PostgreSQL or MySQL
2. **Caching**: Implement Redis for session and cache storage
3. **File Storage**: Use cloud storage (AWS S3, etc.)
4. **CDN**: For static assets delivery

## Final Checklist

Before going live:
- [ ] All environment variables are set
- [ ] Database is properly configured
- [ ] Admin account is created
- [ ] Email configuration is working
- [ ] File uploads are functional
- [ ] SSL certificate is active
- [ ] Domain is properly configured
- [ ] Backups are planned
- [ ] Error monitoring is set up

Your E-Kampot Shop is now ready for production! 🚀
