#!/bin/bash
# ============================================================
# Deploy Script untuk PTrack (Laravel + Flux + Nginx/aaPanel)
# Jalankan di server: bash deploy.sh
# ============================================================

set -e

echo "🚀 Starting deployment..."

# 1. Pull latest code
echo "📥 Pulling latest code..."
git pull origin main

# 2. Install PHP dependencies
echo "📦 Installing PHP dependencies..."
composer install --no-dev --optimize-autoloader

# 3. Install & build JS assets
echo "🎨 Building frontend assets..."
npm ci
npm run build

# 4. Publish Flux JS assets ke public/flux/
echo "⚡ Publishing Flux assets..."
php artisan flux:publish-assets

# 5. Clear & optimize cache
echo "🧹 Optimizing..."
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan event:cache

# 6. Run migrations
echo "🗄️  Running migrations..."
php artisan migrate --force

# 7. Fix permissions (sesuaikan dengan user aaPanel Anda)
echo "🔐 Fixing permissions..."
chmod -R 755 storage bootstrap/cache public/flux

echo ""
echo "✅ Deployment complete!"
echo ""
echo "⚠️  IMPORTANT - Tambahkan config ini di Nginx aaPanel:"
echo "----------------------------------------------------"
echo "location ^~ /flux/ {"
echo "    add_header Cache-Control \"public, max-age=31536000\";"
echo "    try_files \$uri \$uri/ /index.php?\$query_string;"
echo "}"
echo "----------------------------------------------------"
