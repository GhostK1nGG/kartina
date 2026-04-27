# Deploy on Ubuntu 24.04

This project runs with Docker Compose. Use the steps below for the first deploy and for later updates.

## 1. Prepare the server

Install Docker Engine and the Compose plugin using Docker's official Ubuntu docs:

- https://docs.docker.com/engine/install/ubuntu/
- https://docs.docker.com/compose/install/linux/

Minimal command set:

```bash
sudo apt update
sudo apt install ca-certificates curl git
sudo install -m 0755 -d /etc/apt/keyrings
sudo curl -fsSL https://download.docker.com/linux/ubuntu/gpg -o /etc/apt/keyrings/docker.asc
sudo chmod a+r /etc/apt/keyrings/docker.asc

sudo tee /etc/apt/sources.list.d/docker.sources <<EOF
Types: deb
URIs: https://download.docker.com/linux/ubuntu
Suites: $(. /etc/os-release && echo "${UBUNTU_CODENAME:-$VERSION_CODENAME}")
Components: stable
Architectures: $(dpkg --print-architecture)
Signed-By: /etc/apt/keyrings/docker.asc
EOF

sudo apt update
sudo apt install docker-ce docker-ce-cli containerd.io docker-buildx-plugin docker-compose-plugin
sudo systemctl enable --now docker
sudo usermod -aG docker $USER
```

After `usermod`, reconnect to the server.

## 2. Clone the project

```bash
cd /opt
sudo mkdir -p /opt/kartina
sudo chown $USER:$USER /opt/kartina
git clone <GIT_URL> /opt/kartina
cd /opt/kartina
```

## 3. Configure Laravel environment

```bash
cp src/.env.example src/.env
nano src/.env
```

Update at least these values in `src/.env`:

- `APP_ENV=production`
- `APP_DEBUG=false`
- `APP_URL=https://your-domain.tld`
- `ADMIN_NAME=...`
- `ADMIN_EMAIL=...`
- `ADMIN_PASSWORD=...`
- `TELEGRAM_BOT_TOKEN=...`
- `TELEGRAM_CHAT_ID=...`
- `TELEGRAM_WEBHOOK_SECRET=...`
- `PUBLIC_CONTACT_EMAIL=...`
- `PUBLIC_CONTACT_TELEGRAM=...`
- `PUBLIC_CONTACT_VK=...`
- `PUBLIC_CONTACT_INSTAGRAM=...`

Important:

- `DB_HOST=mysql`
- `DB_PORT=3306`
- `DB_DATABASE`, `DB_USERNAME`, and `DB_PASSWORD` must match `MYSQL_DATABASE`, `MYSQL_USER`, and `MYSQL_PASSWORD` in `docker-compose.yml`

## 4. First start

```bash
docker compose up -d --build
docker compose exec app composer install --no-dev --optimize-autoloader
docker compose exec app php artisan key:generate
docker compose exec app php artisan storage:link
docker compose exec app php artisan migrate --force
docker compose exec app php artisan db:seed --class=AdminUserSeeder --force
docker compose exec app npm ci
docker compose exec app npm run build
docker compose exec app php artisan config:cache
docker compose exec app php artisan route:cache
docker compose exec app php artisan view:cache
```

If you need the full demo dataset instead of only the admin user:

```bash
docker compose exec app php artisan db:seed --force
```

## 5. Verify

```bash
docker compose ps
docker compose logs -f app
docker compose logs -f worker
docker compose logs -f nginx
```

With the current `docker-compose.yml`, the services are exposed at:

- `http://SERVER_IP:8080`
- admin panel: `http://SERVER_IP:8080/admin`
- phpMyAdmin: `http://SERVER_IP:8081`

## 6. Deploy updates later

```bash
cd /opt/kartina
git pull
docker compose up -d --build
docker compose exec app composer install --no-dev --optimize-autoloader
docker compose exec app php artisan migrate --force
docker compose exec app npm ci
docker compose exec app npm run build
docker compose exec app php artisan config:cache
docker compose exec app php artisan route:cache
docker compose exec app php artisan view:cache
docker compose exec app php artisan queue:restart
```

## Notes

- The `worker` container already starts automatically and processes the Laravel queue.
- Telegram webhook delivery requires a public `https://` domain for `/telegram/webhook`.
- `docker-compose.yml` currently publishes MySQL on `3307` and phpMyAdmin on `8081`. Do not leave those ports open to the public Internet unless you explicitly want that.
- Docker-published ports can bypass normal `ufw` expectations. See Docker's firewall docs:
  https://docs.docker.com/engine/network/packet-filtering-firewalls/
