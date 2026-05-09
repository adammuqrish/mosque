# Deployment Guide: Laravel to Railway

## Prerequisites
- GitHub account (already set up)
- Railway account (railway.app)

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

### 2.2 Add to Railway Environment Variables later:
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

## Step 4: Railway Setup

### 4.1 Create Railway Account
1. Go to railway.app
2. Sign up with GitHub

### 4.2 Create New Project
1. Click "New Project" → Select "Empty Project"
2. Name it: `mosque-app`

### 4.3 Add MySQL Database
1. Click "Add Plugin" → Select "MySQL"
2. Wait for provisioning → Click "MySQL"
3. Copy the `DATABASE_URL` (format: `mysql://user:pass@host:port/database`)

### 4.4 Add Docker Service
1. Click "Add" → Select "GitHub Repo"
2. Select your Laravel repository
3. **Important**: Select "Docker" as the runtime
4. Railway will automatically detect the `Dockerfile` and build your PHP app

### 4.5 Add Environment Variables
In Railway dashboard for Docker service, add:
In Railway dashboard for PHP service, add:

| Variable | Value |
|----------|-------|
| APP_ENV | production |
| APP_DEBUG | false |
| APP_KEY | (run `php artisan key:generate` locally, copy the key) |
| DB_CONNECTION | mysql |
| DB_HOST | (from Railway MySQL → "Connect" → Hostname) |
| DB_PORT | (from Railway MySQL → "Connect" → Port) |
| DB_DATABASE | (from Railway MySQL → "Connect" → Database) |
| DB_USERNAME | (from Railway MySQL → "Connect" → Username) |
| DB_PASSWORD | (from Railway MySQL → "Connect" → Password) |
| SESSION_DRIVER | database |
| CLOUDINARY_URL | (from Cloudinary dashboard) |

---

## Step 5: Import Database to Railway

### Option A: Using MySQL Client (TablePlus/DBeaver/Workbench)
1. Open your MySQL client
2. Connect using Railway MySQL credentials
3. Import `database_dump.sql`

### Option B: Using Command Line
```bash
# Install Railway CLI
npm install -g @railway/cli

# Login
railway login

# Link to project
railway link

# Get connection and import
mysql -h <hostname> -u <username> -p <database> < database_dump.sql
```

---

## Step 6: Deploy

1. Push your code to GitHub:
```bash
git add .
git commit -m "Prepare for production deployment"
git push origin main
```

2. Railway will auto-deploy on push

3. Visit the generated Railway URL

---

## Step 7: Post-Deployment

### Run Migrations
```bash
railway run php artisan migrate
```

### Clear Cache
```bash
railway run php artisan config:cache
railway run php artisan cache:clear
```

### Test Your App
- Visit the Railway URL
- Test login/registration
- Test all features

---

## Troubleshooting

| Issue | Solution |
|-------|----------|
| "No application key" | Set APP_KEY in Railway dashboard |
| Database connection error | Verify DB_* variables match Railway MySQL |
| File uploads not working | Set CLOUDINARY_URL correctly |
| 500 Error | Check APP_DEBUG=true temporarily for error details |
| Static assets 404 | Run `php artisan config:cache` |

---

## Important Notes

1. **File Storage**: Local storage won't persist on Railway. Use Cloudinary (already configured).

2. **Session**: Now uses database driver (sessions table created).

3. **HTTPS**: Provided automatically by Railway.

4. **Free Tier**: Railway free tier has 500 hours/month. App sleeps after 5 min inactivity.