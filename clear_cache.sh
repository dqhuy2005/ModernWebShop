set -e 

echo "Starting clear..."

PROJECT_PATH=${1:-$(pwd)} 

echo "Dọn cache cho project: $PROJECT_PATH"

cd "$PROJECT_PATH/backend" || exit

php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear

php artisan optimize 

echo "Complete clear cache $(date)"


