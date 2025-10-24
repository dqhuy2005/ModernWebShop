set -e 

echo "Starting clear..."

PROJECT_PATH=${1:-$(pwd)} 

echo "Starting server for project: $PROJECT_PATH"

cd "$PROJECT_PATH/backend" || exit

php artisan serve

echo "Starting Laravel development server..."