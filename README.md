# Bagirata

Aplikasi Laravel untuk mengelola pembagian biaya saat nongkrong bersama teman.

## Setup

```bash
composer install
npm install
cp .env.example .env
php artisan key:generate
php artisan migrate --seed
npm run dev
```