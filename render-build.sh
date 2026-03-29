#!/usr/bin/env bash
# exit on error
set -o errexit
set -o nounset
set -o pipefail

ensure_runtime_database() {
  local db_connection="${DB_CONNECTION:-sqlite}"

  if [ "$db_connection" != "sqlite" ]; then
    return
  fi

  local sqlite_path="${DB_DATABASE:-/var/www/html/database/database.sqlite}"

  if [ "$sqlite_path" = ":memory:" ]; then
    return
  fi

  case "$sqlite_path" in
    /*) ;;
    *)
      sqlite_path="/var/www/html/$sqlite_path"
      ;;
  esac

  local sqlite_dir
  sqlite_dir="$(dirname "$sqlite_path")"

  mkdir -p "$sqlite_dir"
  touch "$sqlite_path"

  # Apache serves requests as www-data, so both the database file and its directory
  # must stay writable for SQLite journals and application writes.
  chown -R www-data:www-data "$sqlite_dir"
  chmod 775 "$sqlite_dir"
  chmod 664 "$sqlite_path"
}

# Install PHP dependencies
composer install --no-dev --optimize-autoloader

# Install Node dependencies
npm install

# Build Node assets
npm run build

ensure_runtime_database

db_connection="${DB_CONNECTION:-sqlite}"

# Clear bootstrap caches that do not require the database.
php artisan config:clear
php artisan route:clear
php artisan view:clear

if [ "$db_connection" = "sqlite" ] || [ "${RUN_MIGRATE_FRESH_SEED:-false}" = "true" ]; then
  # SQLite on Render is demo-style local storage, so always rebuild it to a known-good seeded state.
  # RUN_MIGRATE_FRESH_SEED remains available as an explicit override for other databases.
  php artisan migrate:fresh --seed --force
else
  # Prepare the real Render database schema before the service boots.
  php artisan migrate --force

  # Clear any remaining optimized artifacts now that the database tables exist.
  php artisan optimize:clear

  # Seed the reference data required by public routes and catalog pages.
  for seeder in \
    RbacSeeder \
    CompaniesSeeder \
    CatalogSeeder \
    EnquiryTypeSeeder \
    FaqSeeder \
    ProductTechnicalResourceSeeder \
    QuizeSeeder \
    SupportTicketCategorySeeder
  do
    php artisan db:seed --class="$seeder" --force
  done
fi

php artisan optimize:clear

php artisan config:cache
php artisan route:cache
php artisan view:cache
