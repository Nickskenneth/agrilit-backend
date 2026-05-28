#!/bin/bash
# ============================================================
# AgriLit Backend — VPS Deploy Script
# Jalankan di VPS: bash /var/www/agrilit-backend/deploy.sh
# ============================================================

set -e

cd /var/www/agrilit-backend

echo "=== 1. Pull kode terbaru dari git ==="
git pull origin main

echo ""
echo "=== 2. Install/update dependencies composer ==="
composer install --no-dev --optimize-autoloader

echo ""
echo "=== 3. Clear semua cache ==="
php artisan config:clear
php artisan cache:clear
php artisan route:clear
php artisan view:clear

echo ""
echo "=== 4. Rebuild config cache ==="
php artisan config:cache
php artisan route:cache

echo ""
echo "=== 5. Verifikasi SCOUT settings ==="
echo "SCOUT_DRIVER  = $(grep SCOUT_DRIVER .env | head -1)"
echo "MEILI_HOST    = $(grep MEILISEARCH_HOST .env | head -1)"
echo "MEILI_KEY     = $(grep MEILISEARCH_KEY .env | head -1 | cut -c1-30)..."

echo ""
echo "=== 6. Cek Meilisearch health ==="
curl -s http://localhost:7700/health && echo ""

echo ""
echo "=== 7. Jalankan diagnosa search ==="
php artisan search:diagnose

echo ""
echo "=== DEPLOY SELESAI ==="
