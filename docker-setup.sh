#!/bin/bash

# Reolink Streamer Docker First-Time Setup

set -e

# Colors for output
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
NC='\033[0m' # No Color

print_status() {
    echo -e "${BLUE}[INFO]${NC} $1"
}

print_success() {
    echo -e "${GREEN}[SUCCESS]${NC} $1"
}

print_warning() {
    echo -e "${YELLOW}[WARNING]${NC} $1"
}

print_error() {
    echo -e "${RED}[ERROR]${NC} $1"
}

# Check if Docker is running
check_docker() {
    print_status "Checking Docker installation..."

    if ! command -v docker &> /dev/null; then
        print_error "Docker is not installed. Please install Docker first."
        echo "Visit: https://docs.docker.com/get-docker/"
        exit 1
    fi

    if ! docker info > /dev/null 2>&1; then
        print_error "Docker is not running. Please start Docker first."
        exit 1
    fi

    print_success "Docker is running!"
}

# Setup environment file
setup_env() {
    print_status "Setting up environment configuration..."

    if [ ! -f .env ]; then
        cp .env.docker .env
        print_success "Created .env file from template"
    else
        print_warning ".env file already exists"
    fi
}

# Create required directories
setup_directories() {
    print_status "Creating required directories..."

    mkdir -p database
    mkdir -p storage/logs
    mkdir -p storage/app/public
    mkdir -p storage/framework/{cache,sessions,views}
    mkdir -p bootstrap/cache

    # Create database file
    if [ ! -f database/database.sqlite ]; then
        touch database/database.sqlite
        print_success "Created SQLite database file"
    fi
}

# Generate application key
generate_key() {
    print_status "Generating application key..."

    if ! grep -q "APP_KEY=base64:" .env; then
        # Use a temporary container to generate the key
        APP_KEY=$(docker run --rm -v "$(pwd):/var/www/html" -w /var/www/html php:8.3-cli php -r "
            require 'vendor/autoload.php';
            echo 'base64:' . base64_encode(random_bytes(32));
        " 2>/dev/null || echo "base64:$(openssl rand -base64 32)")

        # Update .env file
        sed -i.bak "s/APP_KEY=.*/APP_KEY=${APP_KEY}/" .env && rm .env.bak
        print_success "Application key generated"
    else
        print_warning "Application key already exists"
    fi
}

# Build and start containers
build_containers() {
    print_status "Building Docker containers (this may take a few minutes)..."

    if docker compose build; then
        print_success "Docker containers built successfully!"
    else
        print_error "Failed to build Docker containers"
        exit 1
    fi
}

# Initialize application
init_app() {
    print_status "Initializing Laravel application..."

    # Start containers temporarily for initialization
    docker compose up -d

    # Wait for containers to be ready
    sleep 10

    # Run initial setup commands
    docker compose exec -T app php artisan key:generate --force || true
    docker compose exec -T app php artisan migrate --force || true
    docker compose exec -T app php artisan storage:link || true

    # Stop containers
    docker compose down

    print_success "Application initialized!"
}

# Main function
main() {
    echo -e "${GREEN}"
    echo "=========================================="
    echo "  Reolink Streamer Docker Setup"
    echo "=========================================="
    echo -e "${NC}"

    check_docker
    setup_env
    setup_directories
    generate_key
    build_containers
    init_app

    echo ""
    echo -e "${GREEN}🎉 Setup Complete! 🎉${NC}"
    echo ""
    echo "Next steps:"
    echo "1. Start development environment: ${BLUE}./docker-run.sh dev${NC}"
    echo "2. Or start production environment: ${BLUE}./docker-run.sh prod${NC}"
    echo "3. Visit: ${BLUE}http://localhost:8080${NC}"
    echo ""
    echo "For more information, see: ${BLUE}DOCKER.md${NC}"
}

main "$@"