# Hướng Dẫn Deploy Laravel Lên VPS (Không Docker)

## Thông Tin VPS
- **IP:** 160.22.160.153
- **Domain:** sancaulong.id.vn
- **OS:** Ubuntu 22.04
- **User:** root

---

## Bước 1: SSH Vào VPS

Mở PowerShell trên Windows:

```bash
ssh root@160.22.160.153
```

Nhập password khi được hỏi.

---

## Bước 2: Cài Đặt Các Package Cần Thiết

### 2.1 Cập nhật hệ thống

```bash
apt update && apt upgrade -y
```

### 2.2 Cài Nginx, MySQL, PHP

```bash
apt install -y nginx mysql-server git curl unzip
```

### 2.3 Cài PHP 8.2 và extensions

```bash
add-apt-repository ppa:ondrej/php -y
apt update
apt install -y php8.2 php8.2-fpm php8.2-mysql php8.2-xml php8.2-mbstring php8.2-curl php8.2-zip php8.2-gd php8.2-bcmath
```

### 2.4 Đặt PHP 8.2 làm mặc định

```bash
update-alternatives --set php /usr/bin/php8.2
```

### 2.5 Cài Composer

```bash
curl -sS https://getcomposer.org/installer | php
mv composer.phar /usr/local/bin/composer
```

### 2.6 Cài Node.js và NPM

```bash
apt install -y nodejs npm
```

---

## Bước 3: Cấu Hình MySQL

### 3.1 Tạo database và user

```bash
mysql
```

Trong MySQL:

```sql
CREATE DATABASE sancaulong;
CREATE USER 'laravel'@'localhost' IDENTIFIED BY 'your_password_here';
GRANT ALL PRIVILEGES ON sancaulong.* TO 'laravel'@'localhost';
FLUSH PRIVILEGES;
EXIT;
```

---

## Bước 4: Clone Code Từ GitHub

```bash
mkdir -p /var/www
cd /var/www
git clone https://github.com/your-username/sancaulong.git
cd sancaulong
```

---

## Bước 5: Cài Đặt Dependencies

```bash
composer install --no-dev --optimize-autoloader
npm install && npm run build
```

---

## Bước 6: Cấu Hình Laravel

### 6.1 Tạo file .env

```bash
cp .env.example .env
nano .env
```

### 6.2 Sửa các thông số trong .env

```env
APP_NAME=DreamSports
APP_ENV=production
APP_DEBUG=false
APP_URL=http://sancaulong.id.vn

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=sancaulong
DB_USERNAME=laravel
DB_PASSWORD=your_password_here
```

Lưu: `Ctrl+X` → `Y` → `Enter`

### 6.3 Generate key và migrate

```bash
php artisan key:generate
php artisan migrate --force
php artisan storage:link
```

### 6.4 Phân quyền

```bash
chown -R www-data:www-data /var/www/sancaulong
chmod -R 775 /var/www/sancaulong/storage
chmod -R 775 /var/www/sancaulong/bootstrap/cache
```

---

## Bước 7: Cấu Hình Nginx

### 7.1 Tạo file config

```bash
nano /etc/nginx/sites-available/sancaulong
```

Paste nội dung:

```nginx
server {
    listen 80;
    server_name sancaulong.id.vn www.sancaulong.id.vn 160.22.160.153;
    root /var/www/sancaulong/public;

    index index.php index.html;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php8.2-fpm.sock;
        fastcgi_index index.php;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        include fastcgi_params;
    }

    location ~ /\.(?!well-known).* {
        deny all;
    }
}
```

Lưu: `Ctrl+X` → `Y` → `Enter`

### 7.2 Kích hoạt site

```bash
ln -s /etc/nginx/sites-available/sancaulong /etc/nginx/sites-enabled/
rm -f /etc/nginx/sites-enabled/default
nginx -t
systemctl reload nginx
```

---

## Bước 8: Import Database (Nếu Có)

### 8.1 Export từ máy local (Windows)

Dùng Laragon/HeidiSQL export file `sancaulong.sql`

### 8.2 Upload lên VPS

```bash
scp D:\path\to\sancaulong.sql root@160.22.160.153:/var/www/sancaulong/
```

### 8.3 Import vào MySQL

```bash
mysql -u laravel -p'your_password' sancaulong < /var/www/sancaulong/sancaulong.sql
```

---

## Bước 9: Cấu Hình DNS

Tại nhà cung cấp domain, thêm bản ghi:

| Type | Host | Value |
|------|------|-------|
| A | @ | 160.22.160.153 |
| A | www | 160.22.160.153 |

---

## Bước 10: Cài phpMyAdmin (Tùy Chọn)

```bash
apt install phpmyadmin -y
ln -s /usr/share/phpmyadmin /var/www/sancaulong/public/phpmyadmin
```

Truy cập: http://sancaulong.id.vn/phpmyadmin

---

## Các Lệnh Hữu Ích

### Cập nhật code mới

```bash
cd /var/www/sancaulong
git pull
composer install --no-dev
npm install && npm run build
php artisan migrate --force
php artisan optimize:clear
```

### Xem log lỗi

```bash
tail -50 /var/www/sancaulong/storage/logs/laravel.log
```

### Restart services

```bash
systemctl restart nginx
systemctl restart php8.2-fpm
systemctl restart mysql
```

### Kiểm tra trạng thái

```bash
systemctl status nginx
systemctl status php8.2-fpm
systemctl status mysql
```

### Clear cache Laravel

```bash
php artisan optimize:clear
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

---

## Troubleshooting

### Lỗi 500 Server Error

```bash
tail -30 /var/www/sancaulong/storage/logs/laravel.log
```

### Lỗi Permission

```bash
chown -R www-data:www-data /var/www/sancaulong
chmod -R 775 /var/www/sancaulong/storage
chmod -R 775 /var/www/sancaulong/bootstrap/cache
```

### Lỗi Database Connection

Kiểm tra file .env:
```bash
cat /var/www/sancaulong/.env | grep DB_
```

### Lỗi Class Not Found (Linux phân biệt chữ hoa/thường)

```bash
composer dump-autoload
php artisan optimize:clear
```

---

## Bảo Mật (Khuyến Nghị)

### Bật Firewall

```bash
ufw allow 22
ufw allow 80
ufw allow 443
ufw enable
```

### Cài SSL (Let's Encrypt)

```bash
apt install certbot python3-certbot-nginx -y
certbot --nginx -d sancaulong.id.vn -d www.sancaulong.id.vn
```

---

## Thông Tin Kết Nối

| Service | Thông tin |
|---------|-----------|
| SSH | `ssh root@160.22.160.153` |
| MySQL | User: `laravel`, DB: `sancaulong` |
| Website | http://sancaulong.id.vn |
| phpMyAdmin | http://sancaulong.id.vn/phpmyadmin |
