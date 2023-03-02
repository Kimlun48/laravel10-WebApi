## install
composer install
cp .env.example .env 
php artisan migrate
php artisan link:storage
