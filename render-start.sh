#!/usr/bin/env bash
set -o errexit
set -o nounset
set -o pipefail

cd /var/www/html

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

ensure_runtime_database

db_connection="${DB_CONNECTION:-sqlite}"

# Reset bootstrap artifacts that do not require the database so runtime env vars are used consistently.
php artisan config:clear
php artisan route:clear
php artisan view:clear

if [ "$db_connection" = "sqlite" ] || [ "${RUN_MIGRATE_FRESH_SEED:-false}" = "true" ]; then
  # SQLite on Render is demo-style local storage, so always rebuild it to a known-good seeded state.
  # RUN_MIGRATE_FRESH_SEED remains available as an explicit override for other databases.
  php artisan migrate:fresh --seed --force
else
  # Apply schema changes against the live Render database before serving traffic.
  php artisan migrate --force

  # Reset any remaining optimized artifacts now that the database schema exists.
  php artisan optimize:clear

  # Seed the shared reference data required by public pages and storefront flows.
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

exec apache2-foreground
