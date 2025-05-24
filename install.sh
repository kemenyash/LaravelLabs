#!/bin/bash

set -e

DOMAIN="pokru.ch"
DB_NAME="pokruch"
DB_USER="pokruch_admin"
DB_PASSWORD="aPt2!vue0A8xkOfy"

echo "✅ Updating system..."
apt update && apt upgrade -y
apt install software-properties-common curl wget unzip gnupg2 -y

echo "✅ Installing PHP 8.2 and modules..."
add-apt-repository ppa:ondrej/php -y
apt update
apt install php8.2 php8.2-fpm php8.2-mysql php8.2-curl php8.2-xml php8.2-mbstring php8.2-zip php8.2-gd php8.2-bcmath php8.2-intl php8.2-soap php8.2-opcache php8.2-redis -y

echo "✅ Installing MariaDB and creating database..."
apt install mariadb-server -y
mysql -e "CREATE DATABASE $DB_NAME CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"
mysql -e "CREATE USER '$DB_USER'@'localhost' IDENTIFIED BY '$DB_PASSWORD';"
mysql -e "GRANT ALL PRIVILEGES ON $DB_NAME.* TO '$DB_USER'@'localhost';"
mysql -e "FLUSH PRIVILEGES;"

echo "✅ Installing Redis..."
apt install redis-server -y
systemctl enable redis --now

echo "✅ Installing Nginx..."
apt install nginx -y

echo "✅ Preparing web root..."
mkdir -p /var/www/$DOMAIN
cd /var/www/$DOMAIN
wget https://wordpress.org/latest.zip
unzip latest.zip
mv wordpress/* .
rm -rf wordpress latest.zip
chown -R www-data:www-data /var/www/$DOMAIN

echo "✅ Generating self-signed SSL..."
mkdir -p /etc/ssl/private /etc/ssl/certs
openssl req -x509 -nodes -days 365 \
  -newkey rsa:2048 \
  -keyout /etc/ssl/private/selfsigned.key \
  -out /etc/ssl/certs/selfsigned.crt \
  -subj "/C=UA/ST=Kyiv/L=Kyiv/O=Pokruch/OU=Web/CN=$DOMAIN"

echo "✅ Creating Nginx config..."
cat > /etc/nginx/sites-available/$DOMAIN <<EOF
server {
    listen 80;
    server_name $DOMAIN www.$DOMAIN;
    return 301 https://\$host\$request_uri;
}

server {
    listen 443 ssl http2;
    server_name $DOMAIN www.$DOMAIN;

    ssl_certificate     /etc/ssl/certs/selfsigned.crt;
    ssl_certificate_key /etc/ssl/private/selfsigned.key;
    ssl_protocols TLSv1.2 TLSv1.3;
    ssl_ciphers HIGH:!aNULL:!MD5;
    add_header Strict-Transport-Security "max-age=63072000" always;
    add_header X-Content-Type-Options nosniff;

    root /var/www/$DOMAIN;
    index index.php index.html;

    access_log /var/log/nginx/${DOMAIN}_access.log;
    error_log /var/log/nginx/${DOMAIN}_error.log;

    location / {
        try_files \$uri \$uri/ /index.php?\$args;
    }

    location ~ \.php\$ {
        include snippets/fastcgi-php.conf;
        fastcgi_pass unix:/run/php/php8.2-fpm.sock;
        fastcgi_param SCRIPT_FILENAME \$document_root\$fastcgi_script_name;
    }

    location ~* \.(js|css|png|jpg|jpeg|gif|ico|svg|webp|woff2?|ttf|eot)$ {
        expires 30d;
        access_log off;
    }

    client_max_body_size 64M;
}
EOF

ln -s /etc/nginx/sites-available/$DOMAIN /etc/nginx/sites-enabled/
nginx -t && systemctl reload nginx

echo "✅ Preparing wp-config.php..."
cp /var/www/$DOMAIN/wp-config-sample.php /var/www/$DOMAIN/wp-config.php

cat >> /var/www/$DOMAIN/wp-config.php <<EOF

define('DB_NAME', '$DB_NAME');
define('DB_USER', '$DB_USER');
define('DB_PASSWORD', '$DB_PASSWORD');
define('DB_HOST', 'localhost');

define('WP_CACHE', true);
define('WP_REDIS_HOST', '127.0.0.1');

EOF

echo "✅ Setup complete! Open https://$DOMAIN to finish installation."
