# Laravel Docker Production Deployment Script for Windows

Write-Host "======================================" -ForegroundColor Green
Write-Host "Laravel Docker Production Deployment" -ForegroundColor Green
Write-Host "======================================`n" -ForegroundColor Green

# Check if .env file exists
if (-Not (Test-Path ".env")) {
    Write-Host "Creating .env file from .env.example..." -ForegroundColor Yellow
    if (Test-Path ".env.example") {
        Copy-Item ".env.example" ".env"
        Write-Host "V .env file created`n" -ForegroundColor Green
        $generateKey = $true
    } else {
        Write-Host "X .env.example not found" -ForegroundColor Red
        exit 1
    }
} else {
    Write-Host "! .env file already exists`n" -ForegroundColor Yellow
    $generateKey = $false
}

# Check if APP_KEY is set
$envContent = Get-Content ".env" -Raw
if ($envContent -notmatch "APP_KEY=base64:") {
    Write-Host "APP_KEY not set, will generate after containers start" -ForegroundColor Yellow
    $generateKey = $true
}

# Update .env for Docker
Write-Host "Configuring .env for Docker..." -ForegroundColor Yellow
$envContent = Get-Content ".env" -Raw
$envContent = $envContent -replace 'DB_HOST=127\.0\.0\.1', 'DB_HOST=mysql'
$envContent = $envContent -replace 'DB_HOST=localhost', 'DB_HOST=mysql'
$envContent = $envContent -replace 'REDIS_HOST=127\.0\.0\.1', 'REDIS_HOST=redis'
$envContent = $envContent -replace 'REDIS_HOST=localhost', 'REDIS_HOST=redis'
Set-Content ".env" $envContent
Write-Host "V .env configured for Docker`n" -ForegroundColor Green

# Stop existing containers
Write-Host "Stopping existing containers..." -ForegroundColor Yellow
docker-compose down
Write-Host "V Containers stopped`n" -ForegroundColor Green

# Build the Docker images
Write-Host "Building Docker images..." -ForegroundColor Yellow
docker-compose build --no-cache
if ($LASTEXITCODE -ne 0) {
    Write-Host "X Docker build failed" -ForegroundColor Red
    exit 1
}
Write-Host "V Docker images built successfully`n" -ForegroundColor Green

# Start containers
Write-Host "Starting containers..." -ForegroundColor Yellow
docker-compose up -d
if ($LASTEXITCODE -ne 0) {
    Write-Host "X Failed to start containers" -ForegroundColor Red
    exit 1
}
Write-Host "V Containers started`n" -ForegroundColor Green

# Wait for services to be ready
Write-Host "Waiting for services to be ready (30 seconds)..." -ForegroundColor Yellow
Start-Sleep -Seconds 30
Write-Host "V Services should be ready`n" -ForegroundColor Green

# Generate APP_KEY if needed
if ($generateKey) {
    Write-Host "Generating application key..." -ForegroundColor Yellow
    docker-compose exec -T app php artisan key:generate --force
    Write-Host "V Application key generated`n" -ForegroundColor Green
}

# Generate JWT secret if needed
Write-Host "Checking JWT secret..." -ForegroundColor Yellow
$envContent = Get-Content ".env" -Raw
if ($envContent -notmatch "JWT_SECRET=") {
    Write-Host "Generating JWT secret..." -ForegroundColor Yellow
    docker-compose exec -T app php artisan jwt:secret --force
    Write-Host "V JWT secret generated`n" -ForegroundColor Green
} else {
    Write-Host "V JWT secret already exists`n" -ForegroundColor Green
}

# Run migrations
Write-Host "Running database migrations..." -ForegroundColor Yellow
docker-compose exec -T app php artisan migrate --force
if ($LASTEXITCODE -ne 0) {
    Write-Host "! Migration may have failed, but continuing...`n" -ForegroundColor Yellow
} else {
    Write-Host "V Migrations completed`n" -ForegroundColor Green
}

# Seed database (optional)
$seed = Read-Host "Do you want to seed the database? (y/N)"
if ($seed -eq "y" -or $seed -eq "Y") {
    Write-Host "Seeding database..." -ForegroundColor Yellow
    docker-compose exec -T app php artisan db:seed --force
    Write-Host "V Database seeded`n" -ForegroundColor Green
}

# Create storage link
Write-Host "Creating storage link..." -ForegroundColor Yellow
docker-compose exec -T app php artisan storage:link
Write-Host "V Storage link created`n" -ForegroundColor Green

# Clear and cache config
Write-Host "Optimizing application..." -ForegroundColor Yellow
docker-compose exec -T app php artisan config:cache
docker-compose exec -T app php artisan route:cache
docker-compose exec -T app php artisan view:cache
Write-Host "V Application optimized`n" -ForegroundColor Green

# Set correct permissions
Write-Host "Setting permissions..." -ForegroundColor Yellow
docker-compose exec -T app chown -R www-data:www-data /var/www/html/storage
docker-compose exec -T app chown -R www-data:www-data /var/www/html/bootstrap/cache
docker-compose exec -T app chmod -R 775 /var/www/html/storage
docker-compose exec -T app chmod -R 775 /var/www/html/bootstrap/cache
Write-Host "V Permissions set`n" -ForegroundColor Green

Write-Host "======================================" -ForegroundColor Green
Write-Host "Deployment completed successfully!" -ForegroundColor Green
Write-Host "======================================`n" -ForegroundColor Green
Write-Host "Application is running at: " -NoNewline
Write-Host "http://localhost:8080" -ForegroundColor Yellow
Write-Host "`nUseful commands:"
Write-Host "  View logs:    " -NoNewline; Write-Host "docker-compose logs -f app" -ForegroundColor Yellow
Write-Host "  Stop:         " -NoNewline; Write-Host "docker-compose down" -ForegroundColor Yellow
Write-Host "  Restart:      " -NoNewline; Write-Host "docker-compose restart app" -ForegroundColor Yellow
Write-Host "  Shell access: " -NoNewline; Write-Host "docker-compose exec app bash" -ForegroundColor Yellow
Write-Host ""
