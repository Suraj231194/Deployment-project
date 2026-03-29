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

  mkdir -p "$(dirname "$sqlite_path")"
  touch "$sqlite_path"
}

ensure_runtime_database

# Reset bootstrap artifacts that do not require the database so runtime env vars are used consistently.
php artisan config:clear
php artisan route:clear
php artisan view:clear

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

php artisan config:cache
php artisan route:cache
php artisan view:cache

exec apache2-foreground
