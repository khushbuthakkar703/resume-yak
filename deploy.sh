#!/usr/bin/env bash
cd /home/yak/www/test.resumeyak.com/builder/
composer install
npm install
npm run dev
cp ../../config/.env ./
cp ../../config/database.php ./config/
php artisan migrate
exit
