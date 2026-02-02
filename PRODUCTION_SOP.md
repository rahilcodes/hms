# 🚢 HMS Production Architecture & Deployment SOP

This document serves as the **Standard Operating Procedure (SOP)** for the Hotel Management System (HMS). It covers the specific Traefik + Nginx + Laravel architecture implemented for production.

## 🏗️ Architecture Overview

The system uses a **Mixed Proxy Architecture** to allow co-existence with other services (like n8n) while maintaining high security (SSL/HTTPS).

```text
Internet (hms.creativals.com)
   |
   | (Ports 80/443)
   v
[Traefik Docker]  <-- Certificates/SSL Termination
   |
   | (Internal Proxy to 127.0.0.1:8081)
   v
[Host Nginx]      <-- Application Server (FastCGI)
   |
   └── [Laravel Cache/Assets]
```

## 🛠️ Critical Components

### 1. HTTPS Enforcement
To prevent mixed-content errors (CSS not loading), Laravel is configured to force HTTPS in production via `AppServiceProvider.php`:
```php
if (app()->environment('production')) {
    URL::forceScheme('https');
}
```

### 2. Vite Asset manifest
In production, the UI relies on compiled assets. If the styling breaks, ensure you have run:
```bash
npm install
npm run build
```
This generates `public/build/manifest.json`, which Laravel uses to map assets.

### 3. Traefik Dynamic Configuration (`/traefik/dynamic/hms.yml`)
Traefik routes traffic to the host's Nginx on port **8081**.
```yaml
http:
  routers:
    hms:
      rule: "Host(`hms.creativals.com`)"
      entryPoints:
        - websecure
      service: hms
      tls: {}

  services:
    hms:
      loadBalancer:
        servers:
          - url: "http://127.0.0.1:8081"
```

## 📋 Deployment Checklist (New Domain/Subdomain)

If you change the domain or add a new one in the future, follow this path:

1.  **DNS**: Direct the A/CNAME record to the VPS IP (`69.62.81.21`).
2.  **Environment**: Update `.env` with the new `APP_URL` and `ASSET_URL`.
3.  **Traefik Router**: Update the `rule: Host(...)` in your `hms.yml` inside the Traefik dynamic configuration folder.
4.  **Laravel Cache**: After any `.env` change, run:
    ```bash
    php artisan config:cache
    php artisan route:cache
    ```

## 🔒 Permissions Policy
Always ensure `www-data` owns the critical folders:
```bash
chown -R www-data:www-data /var/www/hms/storage /var/www/hms/bootstrap/cache
```

---
*Created on 2026-02-02 based on the Production Deployment Milestone.*
