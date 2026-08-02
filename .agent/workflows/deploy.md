---
description: Comprehensive Deployment Guide (Local to Production)
---

# 🚀 Professional Deployment Guide

Follow these steps to move your project from your local Laragon environment to a production server via GitHub.

## Phase 1: Local Preparation
// turbo
1. **Build Production Assets**:
   Run this to compile all Tailwind CSS and JavaScript for the server:
   ```bash
   npm run build
   ```

2. **Clean Project**:
   Remove any temporary files or dev-only logs:
   ```bash
   php artisan config:clear
   php artisan route:clear
   php artisan view:clear
   ```

3. **Check .gitignore**:
   Ensure your `.env` and `node_modules` are ignored so they don't leak to GitHub.

## Phase 2: GitHub Repository
1. **Initialize & Push**:
   ```bash
   git init
   git add .
   git commit -m "chore: initial production-ready commit"
   git remote add origin YOUR_REPO_URL
   git push -u origin main
   ```

## Phase 3: Server Requirements
Ensure your server (VPS like DigitalOcean, Linode, or AWS) has:
- **PHP 8.2+** (with extensions: bcmath, curl, mbstring, openssl, xml, zip)
- **Nginx or Apache**
- **MySQL 8.0+**
- **Composer**
- **Node.js & NPM** (Optional if you pre-build locally)

## Phase 4: Server Deployment
1. **Clone the Repo**:
   ```bash
   cd /var/www
   git clone YOUR_REPO_URL hotel-booking
   cd hotel-booking
   ```

2. **Install Dependencies**:
   ```bash
   composer install --optimize-autoloader --no-dev
   ```

3. **Environment Setup**:
   ```bash
   cp .env.example .env
   nano .env
   ```
   *Edit `APP_ENV=production`, `APP_DEBUG=false`, `APP_URL`, and your live `DB_*` credentials.*

4. **Security & Data**:
   ```bash
   php artisan key:generate
   php artisan storage:link
   php artisan migrate --force
   ```

5. **Permission Management**:
   ```bash
   sudo chown -R www-data:www-data storage bootstrap/cache
   sudo chmod -R 775 storage bootstrap/cache
   ```

## Phase 5: Production Optimization
Run these to make the app lightning fast on the server:
```bash
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

## Phase 6: Web Server Configuration (Nginx)
Point your root to the `public/` directory:
```nginx
server {
    listen 80;
    server_name yourdomain.com;
    root /var/www/hotel-booking/public;

    add_header X-Frame-Options "SAMEORIGIN";
    add_header X-Content-Type-Options "nosniff";

    index index.php;

    charset utf-8;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php8.2-fpm.sock;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        include fastcgi_params;
    }
}
```

---
**Need help with a specific server (e.g. Hostinger, DigitalOcean)? Just ask!**
