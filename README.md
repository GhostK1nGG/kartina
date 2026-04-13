# Laravel 11 Local Environment

## Stack

- Laravel 11
- PHP 8.3 (PHP-FPM container)
- Nginx
- MySQL 8
- phpMyAdmin
- Vite
- Tailwind CSS
- Alpine.js
- Docker Compose

## Start

```bash
docker compose up -d --build
docker compose exec app composer install
docker compose exec app php artisan key:generate
docker compose exec app php artisan migrate
docker compose exec app npm install
docker compose exec app npm run build
```

## Queue

Telegram-уведомления отправляются через Laravel Queue.

Вручную:

```bash
docker compose exec app php artisan queue:work --sleep=1 --tries=3 --timeout=90
```

Автоматически:

- в `docker-compose.yml` добавлен сервис `worker`
- он поднимается вместе со стеком при `docker compose up -d --build`
- проверить состояние можно командой:

```bash
docker compose ps
```

## URLs

- Site: http://localhost:8080
- phpMyAdmin: http://localhost:8081

## Database

- Host: ``
- Port: ``
- Database: ``
- User: ``
- Password: ``
- Root password: ``
