# Deployment Guide: Docker (DigitalOcean Droplet or any Docker host)

> **Note:** The app was previously deployed on Railway. Railway setup has been removed.
> Mail still uses the Resend HTTP API transport (works on any host) — see Mail section below.

## Prerequisites
- GitHub account (already set up)
- A server with Docker (DigitalOcean droplet — see `deploy/droplet-setup.sh`) or any container platform

---

## Step 1: Local Setup

### 1.1 Install Dependencies
```bash
cd C:\laragonbaru\www\mosque-Prod
composer install
```

### 1.2 Generate Application Key (if not exists)
```bash
php artisan key:generate
```

### 1.3 Create Storage Symlink
```bash
php artisan storage:link
```

---

## Step 2: Cloudinary Setup (for file uploads)

### 2.1 Create Cloudinary Account
1. Go to cloudinary.com
2. Sign up for free account
3. Copy your `Cloudinary URL` from Dashboard

### 2.2 Add to server environment variables later:
```
CLOUDINARY_URL=cloudinary://API_KEY:API_SECRET@CLOUD_NAME
```

---

## Step 3: Export Local MySQL Database

### 3.1 Open Laragon Terminal or MySQL CLI

### 3.2 Export database:
```bash
mysqldump -u root -p mosque > database_dump.sql
```

**Note:** Replace `mosque` with your actual database name from `.env`

---

## Step 4: Server Setup

### 4.1 Provision the Droplet
Run the setup script on a fresh Ubuntu droplet:
```bash
bash deploy/droplet-setup.sh
```

### 4.2 Configure Environment Variables
Provide on the server (systemd unit, `.env`, or shell export):

| Variable | Value |
|----------|-------|
| APP_ENV | production |
| APP_DEBUG | false |
| APP_KEY | (run `php artisan key:generate` locally, copy the key) |
| DB_CONNECTION | mysql |
| DB_HOST | (MySQL host) |
| DB_PORT | (MySQL port) |
| DB_DATABASE | (database name) |
| DB_USERNAME | (db user) |
| DB_PASSWORD | (db password) |
| SESSION_DRIVER | database |
| CLOUDINARY_URL | (from Cloudinary dashboard) |

The container accepts either discrete `DB_*` variables or a single
`DATABASE_URL=mysql://user:pass@host:port/database`.

---

## Step 5: Import Database to Server

Using a MySQL client (TablePlus/DBeaver/Workbench) or command line:
```bash
mysql -h <hostname> -u <username> -p <database> < database_dump.sql
```

---

## Step 6: Deploy

1. Push your code to GitHub:
```bash
git push origin main
```

2. On the server, pull and rebuild:
```bash
git pull origin main
docker compose up -d --build   # or your usual build/run command
```

The `start.sh` entrypoint automatically:
- Generates/validates `APP_KEY`
- Runs pending migrations
- Seeds the database only if empty
- Rebuilds config cache

---

## Step 7: Post-Deployment

### Run Migrations (automatic on boot, or manually)
```bash
docker exec <container> php artisan migrate --force
```

### Clear Cache
```bash
docker exec <container> php artisan config:cache
docker exec <container> php artisan cache:clear
```

### Test Your App
- Visit the server URL
- Test login/registration
- Test all features

---

## Mail (Resend HTTP API)

Outbound email uses a custom Resend HTTP API transport (`app/Transports/ResendTransport.php`)
instead of SMTP, configured via `MAIL_MAILER=resend` + `RESEND_API_KEY`. This was originally
introduced because Railway blocks outbound SMTP ports, but it works on any host and remains
the configured mailer. Brevo transport (`brevo`) is also available as an alternative.

---

## Troubleshooting

| Issue | Solution |
|-------|----------|
| "No application key" | Set APP_KEY in the server environment |
| Database connection error | Verify DB_* variables match your MySQL instance |
| File uploads not working | Set CLOUDINARY_URL correctly |
| 500 Error | Check APP_DEBUG=true temporarily for error details |
| Static assets 404 | Run `php artisan config:cache` |

---

## Important Notes

1. **File Storage**: Container filesystem is ephemeral. Use Cloudinary (already configured) for uploads.

2. **Session**: Uses the database driver (sessions table created).

3. **HTTPS**: Terminate at Cloudflare/nginx in front of the container (see `deploy/cloudflared-quicktunnel.service`).
