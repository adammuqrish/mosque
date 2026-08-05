#!/bin/bash
# =============================================================================
#  mosque system — DigitalOcean droplet provisioning script
#  Run ONCE as root on the droplet:
#      sudo bash /opt/droplet-setup.sh
#  (or copy the repo first:  scp deploy/droplet-setup.sh root@YOUR_IP:/opt/)
# =============================================================================
set -euo pipefail

REPO_URL="https://github.com/adammuqrish/mosque.git"
APP_DIR="/opt/mosque"
APP_PORT=8080

echo "==> 1/9  Preflight checks"
if [ "$(id -u)" -ne 0 ]; then
    echo "ERROR: run as root (sudo -i or sudo bash)." >&2
    exit 1
fi
. /etc/os-release
if [ "$ID" != "ubuntu" ]; then
    echo "WARNING: script targets Ubuntu; you are on $ID. Continuing anyway..." >&2
fi

echo "==> 2/9  Swap file (1G)"
if [ ! -f /swapfile ]; then
    fallocate -l 1G /swapfile
    chmod 600 /swapfile
    mkswap /swapfile >/dev/null
    swapon /swapfile
    grep -q '/swapfile' /etc/fstab || echo '/swapfile none swap sw 0 0' >> /etc/fstab
    echo "    swapfile created"
else
    echo "    /swapfile already exists, skipping"
fi

echo "==> 3/9  Timezone"
timedatectl set-timezone Asia/Kuala_Lumpur 2>/dev/null || true
echo "    set to Asia/Kuala_Lumpur"

echo "==> 4/9  Docker"
apt-get install -y git >/dev/null 2>&1 || true
if ! command -v docker >/dev/null 2>&1; then
    apt-get update -y
    apt-get install -y ca-certificates curl gnupg
    install -m 0755 -d /etc/apt/keyrings
    curl -fsSL https://download.docker.com/linux/ubuntu/gpg -o /etc/apt/keyrings/docker.asc
    chmod a+r /etc/apt/keyrings/docker.asc
    echo "deb [arch=$(dpkg --print-architecture) signed-by=/etc/apt/keyrings/docker.asc] https://download.docker.com/linux/ubuntu $(. /etc/os-release && echo "$VERSION_CODENAME") stable" \
        > /etc/apt/sources.list.d/docker.list
    apt-get update -y
    apt-get install -y docker-ce docker-ce-cli containerd.io docker-compose-plugin
    systemctl enable --now docker
    echo "    docker installed"
else
    echo "    docker already installed"
fi
usermod -aG docker "${SUDO_USER:-root}" 2>/dev/null || true

echo "==> 5/9  MySQL"
if ! command -v mysql >/dev/null 2>&1; then
    DEBIAN_FRONTEND=noninteractive apt-get install -y mysql-server
    systemctl enable --now mysql
    echo "    mysql installed"
else
    echo "    mysql already installed"
fi

DB_PASS="${DB_PASS:-}"
if [ -z "$DB_PASS" ]; then
    DB_PASS="$(openssl rand -base64 18 | tr -d '/+=')"
    echo "    Generated MySQL app password: $DB_PASS"
fi
mysql -uroot -e "CREATE DATABASE IF NOT EXISTS mosque CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"
mysql -uroot -e "CREATE USER IF NOT EXISTS 'mosque'@'localhost' IDENTIFIED BY '${DB_PASS}';"
mysql -uroot -e "ALTER USER 'mosque'@'localhost' IDENTIFIED BY '${DB_PASS}';"
mysql -uroot -e "GRANT ALL PRIVILEGES ON mosque.* TO 'mosque'@'localhost'; FLUSH PRIVILEGES;"
echo "    database 'mosque' + user 'mosque' ready"

echo "==> 6/9  Firewall (ufw)"
if command -v ufw >/dev/null 2>&1; then
    ufw allow OpenSSH >/dev/null 2>&1 || ufw allow 22/tcp >/dev/null 2>&1
    ufw allow 80/tcp  >/dev/null 2>&1
    ufw allow 443/tcp >/dev/null 2>&1
    ufw --force enable >/dev/null 2>&1 || true
    echo "    allowed 22/80/443"
else
    echo "    ufw not present, skipping (ensure your firewall allows 22/80/443)"
fi

echo "==> 7/9  cloudflared (Cloudflare Tunnel)"
if ! command -v cloudflared >/dev/null 2>&1; then
    curl -fsSL https://pkg.cloudflare.com/cloudflare-main.gpg -o /usr/share/keyrings/cloudflare-main.gpg 2>/dev/null || \
    curl -fsSL https://pkg.cloudflare.com/cloudflare-archive.key.gpg -o /usr/share/keyrings/cloudflare-main.gpg 2>/dev/null
    echo "deb [signed-by=/usr/share/keyrings/cloudflare-main.gpg] https://pkg.cloudflare.com/cloudflared $(lsb_release -cs) main" > /etc/apt/sources.list.d/cloudflared.list 2>/dev/null || true
    apt-get update -y
    apt-get install -y cloudflared || { echo "WARNING: cloudflared install failed — install manually (see https://developers.cloudflare.com/cloudflare-one/connections/connect-networks/downloads/)."; }
    echo "    cloudflared installed"
else
    echo "    cloudflared already installed"
fi

echo "==> 8/9  Clone + configure app"
if [ ! -d "$APP_DIR/.git" ]; then
    git clone "$REPO_URL" "$APP_DIR"
    echo "    cloned to $APP_DIR"
else
    git -C "$APP_DIR" pull --ff-only || true
    echo "    $APP_DIR already exists — pulled latest"
fi

APP_KEY="base64:$(openssl rand -base64 32 | tr -d '\n')"
if [ ! -f "$APP_DIR/.env" ]; then
    cp "$APP_DIR/deploy/.env.droplet.example" "$APP_DIR/.env"
    sed -i "s|^APP_KEY=.*|APP_KEY=${APP_KEY}|" "$APP_DIR/.env"
    sed -i "s|^DB_DATABASE=.*|DB_DATABASE=mosque|" "$APP_DIR/.env"
    sed -i "s|^DB_USERNAME=.*|DB_USERNAME=mosque|" "$APP_DIR/.env"
    sed -i "s|^DB_PASSWORD=.*|DB_PASSWORD=${DB_PASS}|" "$APP_DIR/.env"
    echo "    .env created (APP_KEY + DB credentials injected)"
else
    echo "    .env already exists — left untouched"
fi

echo "==> 9/9  Build + run container"
cd "$APP_DIR"
docker build -t mosque .
docker rm -f mosque >/dev/null 2>&1 || true
# host network: container uses the droplet's own 127.0.0.1 for MySQL (DB_HOST=127.0.0.1)
# and nginx binds port 8080 directly on the host (protected by ufw; Cloudflare Tunnel targets 127.0.0.1:8080)
docker run -d \
    --name mosque \
    --restart=unless-stopped \
    --network host \
    -v mosque-storage:/var/www/html/storage/app/public \
    --env-file "$APP_DIR/.env" \
    mosque
echo ""
echo "=============================================================="
echo "  App started. Check logs:  docker logs -f mosque"
echo "  Health check:  curl -s http://127.0.0.1:${APP_PORT}/ | head"
echo ""
echo "  NEXT: point Cloudflare Tunnel at http://127.0.0.1:${APP_PORT}"
echo "    cloudflared tunnel login"
echo "    cloudflared tunnel create mosque"
echo "    cloudflared tunnel route dns mosque YOUR-DOMAIN"
echo "    cloudflared tunnel run mosque"
echo "  (Add an ingress rule: YOUR-DOMAIN -> http://127.0.0.1:${APP_PORT})"
echo "=============================================================="
