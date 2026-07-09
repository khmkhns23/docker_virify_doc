#!/bin/bash
set -e

# Wait for PostgreSQL database
echo "Waiting for database to be ready..."
until pg_isready -h db -U doc_verify_user -d doc_verify_db; do
  echo "Database is unavailable - sleeping"
  sleep 2
done
echo "Database is up!"

# Initialize Laravel files if not present
if [ ! -f .env ]; then
  echo "Copying .env.example to .env..."
  cp .env.example .env
fi

# Run Composer Install if vendor is missing
if [ ! -d vendor ]; then
  echo "Installing Composer dependencies..."
  composer install --no-interaction --prefer-dist --optimize-autoloader
fi

# Generate app key if empty
if ! grep -q "APP_KEY=base64:" .env || [ -z "$(grep APP_KEY .env | cut -d '=' -f2)" ]; then
  echo "Generating application key..."
  php artisan key:generate --force
fi

# Run Migrations & Seeding
echo "Running migrations..."
php artisan migrate --force

echo "Running seeders..."
php artisan db:seed --force

# Set directory permissions for web-server access
echo "Setting storage permissions..."
chmod -R 775 storage bootstrap/cache
chown -R www-data:www-data storage bootstrap/cache

# Asynchronously pull Ollama model
echo "Requesting Ollama to pull qwen2.5:3b model in background..."
curl -s -X POST http://ollama:11434/api/pull -d '{"name": "qwen2.5:3b"}' > /dev/null &

# Execute CMD
exec "$@"
