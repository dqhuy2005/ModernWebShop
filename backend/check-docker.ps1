# Docker Production Deployment - Quick Start

Write-Host "`n========================================" -ForegroundColor Cyan
Write-Host "  Docker Production Deployment Checker" -ForegroundColor Cyan
Write-Host "========================================`n" -ForegroundColor Cyan

# Check if Docker is installed
Write-Host "Checking Docker installation..." -ForegroundColor Yellow
try {
    $dockerVersion = docker --version 2>&1
    if ($LASTEXITCODE -eq 0) {
        Write-Host "V Docker is installed: $dockerVersion" -ForegroundColor Green
    } else {
        Write-Host "X Docker is not installed" -ForegroundColor Red
        Write-Host "Please install Docker Desktop from: https://www.docker.com/products/docker-desktop" -ForegroundColor Yellow
        exit 1
    }
} catch {
    Write-Host "X Docker is not installed" -ForegroundColor Red
    Write-Host "Please install Docker Desktop from: https://www.docker.com/products/docker-desktop" -ForegroundColor Yellow
    exit 1
}

# Check if Docker is running
Write-Host "`nChecking if Docker is running..." -ForegroundColor Yellow
try {
    docker ps 2>&1 | Out-Null
    if ($LASTEXITCODE -eq 0) {
        Write-Host "V Docker is running" -ForegroundColor Green
    } else {
        Write-Host "X Docker is not running" -ForegroundColor Red
        Write-Host "Please start Docker Desktop and try again" -ForegroundColor Yellow
        Write-Host "Attempting to start Docker Desktop..." -ForegroundColor Yellow
        Start-Process "C:\Program Files\Docker\Docker\Docker Desktop.exe" -ErrorAction SilentlyContinue
        Write-Host "Waiting for Docker to start (30 seconds)..." -ForegroundColor Yellow
        Start-Sleep -Seconds 30

        docker ps 2>&1 | Out-Null
        if ($LASTEXITCODE -ne 0) {
            Write-Host "X Docker failed to start. Please start it manually." -ForegroundColor Red
            exit 1
        }
        Write-Host "V Docker started successfully" -ForegroundColor Green
    }
} catch {
    Write-Host "X Docker is not running" -ForegroundColor Red
    Write-Host "Please start Docker Desktop and try again" -ForegroundColor Yellow
    exit 1
}

# Check if docker-compose is available
Write-Host "`nChecking Docker Compose..." -ForegroundColor Yellow
try {
    $composeVersion = docker-compose --version 2>&1
    if ($LASTEXITCODE -eq 0) {
        Write-Host "V Docker Compose is available: $composeVersion" -ForegroundColor Green
    } else {
        # Try docker compose (without hyphen)
        $composeVersion = docker compose version 2>&1
        if ($LASTEXITCODE -eq 0) {
            Write-Host "V Docker Compose is available: $composeVersion" -ForegroundColor Green
        } else {
            Write-Host "X Docker Compose is not available" -ForegroundColor Red
            exit 1
        }
    }
} catch {
    Write-Host "X Docker Compose is not available" -ForegroundColor Red
    exit 1
}

Write-Host "`n========================================" -ForegroundColor Green
Write-Host "  All checks passed! Ready to deploy" -ForegroundColor Green
Write-Host "========================================`n" -ForegroundColor Green

Write-Host "You can now run the deployment script:" -ForegroundColor Cyan
Write-Host "  .\deploy.ps1" -ForegroundColor Yellow

$deploy = Read-Host "`nDo you want to start deployment now? (Y/n)"
if ($deploy -eq "" -or $deploy -eq "y" -or $deploy -eq "Y") {
    Write-Host "`nStarting deployment...`n" -ForegroundColor Green
    & .\deploy.ps1
} else {
    Write-Host "`nDeployment cancelled. Run deploy.ps1 when ready." -ForegroundColor Yellow
}
