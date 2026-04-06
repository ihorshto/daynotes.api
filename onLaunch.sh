#!/bin/bash

# Create .env if not exist
cd /var/www/html/
if test -f ".env"; then
    echo ".env file exist. No need to create one. Container is starting"
else
    echo ".env file does not exist. Creating one. Please check if all fields are filled correctly"
    cp .env.example .env
fi

# Install dependencies with composer
composer install --ignore-platform-reqs --no-interaction --no-plugins --no-scripts

# Dump autoload
composer dump-autoload

# Install npm dependencies and build assets
npm install && npm run build

# Generate application key
php artisan key:generate

# Migrate the database
php artisan migrate

# Ensure correct permissions for storage and cache directories
chmod -R 775 storage bootstrap/cache

# Clear various caches
php artisan cache:clear
php artisan config:clear
php artisan view:clear

# Create a symbolic link for storage
php artisan storage:link

# Publish vendor assets
php artisan vendor:publish --tag=request-docs-config


COLOR="\e[95m"
ENDCOLOR="\e[0m"

echo -e "${COLOR}𝓛𝓪𝓻𝓪𝓿𝓮𝓵 𝓲𝓼 𝓼𝓽𝓪𝓻𝓽𝓲𝓷𝓰${ENDCOLOR}"
