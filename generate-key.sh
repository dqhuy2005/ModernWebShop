#!/bin/bash
set -e

echo "🔑 Generating Laravel Application Key..."

# Generate APP_KEY if not exists
if [ -z "$APP_KEY" ] || [ "$APP_KEY" = "base64:AAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAA=" ]; then
    echo "Generating new APP_KEY..."
    php artisan key:generate --force --show
else
    echo "APP_KEY already set"
fi

echo "✅ Application key ready"
