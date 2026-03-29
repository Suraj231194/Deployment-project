#!/usr/bin/env bash
set -o errexit
set -o nounset
set -o pipefail

cd /var/www/html

# Reset cached bootstrap artifacts so runtime env vars are used consistently.
php artisan optimize:clear

# Apply schema changes against the live Render database before serving traffic.
php artisan migrate --force

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
