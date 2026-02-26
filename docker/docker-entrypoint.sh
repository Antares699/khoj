#!/bin/bash
set -e

echo "Waiting for MySQL to be ready..."
while ! nc -z db 3306; do
  sleep 1
done
echo "MySQL is ready!"

sleep 5

echo "Checking if database needs seeding..."
BUSINESS_COUNT=$(mysql -h db -u root -pkhoj_root_pass khoj_db -N -B -e "SELECT COUNT(*) FROM businesses;" 2>/dev/null || echo "0")

if [ "$BUSINESS_COUNT" -eq "0" ]; then
    echo "No businesses found. Running seeder..."
    php /var/www/html/scripts/seed_osm.php
    echo "Seeding complete!"
else
    echo "Database already has $BUSINESS_COUNT businesses. Skipping seed."
fi

echo "Starting Apache..."
exec apache2-foreground
