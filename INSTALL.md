# Mini CMS Installation Guide

## Requirements

- PHP >= 8.1
- MySQL >= 5.7 or MariaDB >= 10.3
- Composer (for development only)
- Web Server (Apache/Nginx)

### Required PHP Extensions
- pdo, pdo_mysql
- mbstring
- openssl
- json
- curl
- zip
- fileinfo
- gd

---

## Quick Install (Recommended)

1. **Upload files** to your web server
2. **Create MySQL database** 
3. **Visit** `http://your-domain.com/install`
4. **Follow the wizard**:
   - Check server requirements
   - Configure database connection
   - Create admin account
5. **Done!** Access admin at `/admin`

---

## Manual Installation

### Step 1: Upload Files

Upload all files to your web server. The `public` folder should be your document root.

**For shared hosting (cPanel):**
```
public_html/          ← Point domain here
├── index.php
├── .htaccess
└── ... (contents of public/)

/home/user/mini-cms/  ← One level up
├── app/
├── bootstrap/
├── config/
├── database/
├── resources/
├── routes/
├── storage/
├── vendor/
└── ...
```

Update `public/index.php`:
```php
require __DIR__.'/../mini-cms/vendor/autoload.php';
$app = require_once __DIR__.'/../mini-cms/bootstrap/app.php';
```

### Step 2: Create Database

```sql
CREATE DATABASE mini_cms CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
```

### Step 3: Configure Environment

Copy `.env.example` to `.env` and update:

```env
APP_NAME="Your Site Name"
APP_ENV=production
APP_DEBUG=false
APP_URL=https://your-domain.com

DB_HOST=localhost
DB_DATABASE=mini_cms
DB_USERNAME=your_db_user
DB_PASSWORD=your_db_password
```

### Step 4: Generate App Key

```bash
php artisan key:generate
```

### Step 5: Run Migrations

```bash
php artisan migrate
```

### Step 6: Create Admin User

```bash
php artisan tinker
>>> App\Models\User::create([
    'name' => 'Admin',
    'email' => 'admin@example.com',
    'password' => bcrypt('your-password'),
    'role' => 'admin',
    'is_active' => true
]);
```

### Step 7: Set Permissions

```bash
chmod -R 755 storage bootstrap/cache
```

---

## VPS Installation (Ubuntu/Nginx)

### 1. Install Requirements

```bash
sudo apt update
sudo apt install nginx mysql-server php8.1-fpm php8.1-mysql php8.1-mbstring php8.1-xml php8.1-curl php8.1-zip php8.1-gd
```

### 2. Configure Nginx

```nginx
server {
    listen 80;
    server_name your-domain.com;
    root /var/www/mini-cms/public;
    index index.php;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php8.1-fpm.sock;
        fastcgi_index index.php;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        include fastcgi_params;
    }

    location ~ /\.(?!well-known).* {
        deny all;
    }
}
```

### 3. Set Ownership

```bash
sudo chown -R www-data:www-data /var/www/mini-cms
sudo chmod -R 755 /var/www/mini-cms/storage
```

---

## Troubleshooting

### 500 Internal Server Error
- Check `storage/logs/laravel.log` for details
- Ensure `storage/` and `bootstrap/cache/` are writable
- Verify `.env` file exists and is configured

### Database Connection Failed
- Verify database credentials in `.env`
- Ensure MySQL is running
- Check if user has proper permissions

### Blank Page
- Enable `APP_DEBUG=true` temporarily to see errors
- Check PHP error logs

---

## Post-Installation

1. **Remove installer**: Delete `storage/installed` to reinstall (clears DB first!)
2. **Configure email**: Update MAIL_* settings in `.env` for contact notifications
3. **Set up cron** (optional): For scheduled tasks
   ```bash
   * * * * * cd /path-to-mini-cms && php artisan schedule:run >> /dev/null 2>&1
   ```

---

## Support

For issues and feature requests, contact the developer.
