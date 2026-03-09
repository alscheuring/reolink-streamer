#!/bin/bash

# Reolink Streamer Docker Management Script

set -e

# Colors for output
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
NC='\033[0m' # No Color

# Functions
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
    if ! docker info > /dev/null 2>&1; then
        print_error "Docker is not running. Please start Docker first."
        exit 1
    fi
}

# Setup environment file
setup_env() {
    if [ ! -f .env ]; then
        print_status "Creating .env file from .env.docker template..."
        cp .env.docker .env

        # Generate APP_KEY
        print_status "Generating application key..."
        docker run --rm -v "$(pwd):/var/www/html" -w /var/www/html php:8.3-cli php artisan key:generate --force --no-interaction

        print_success ".env file created and configured!"
    else
        print_warning ".env file already exists. Skipping creation."
    fi
}

# Create database file
setup_database() {
    if [ ! -f database/database.sqlite ]; then
        print_status "Creating SQLite database file..."
        touch database/database.sqlite
        print_success "Database file created!"
    else
        print_warning "Database file already exists. Skipping creation."
    fi
}

# Main execution
main() {
    print_status "Starting Reolink Streamer Docker setup..."

    check_docker
    setup_env
    setup_database

    case "${1:-dev}" in
        "dev"|"development")
            print_status "Starting development environment..."
            docker-compose up --build
            ;;
        "prod"|"production")
            print_status "Starting production environment..."
            docker-compose -f docker-compose.yml -f docker-compose.prod.yml up --build -d
            print_success "Production environment started!"
            print_status "Application available at: http://localhost:8080"
            ;;
        "build")
            print_status "Building Docker images..."
            docker-compose build
            print_success "Build completed!"
            ;;
        "down")
            print_status "Stopping containers..."
            docker-compose down
            print_success "Containers stopped!"
            ;;
        "clean")
            print_status "Cleaning up Docker resources..."
            docker-compose down --volumes --remove-orphans
            docker system prune -f
            print_success "Cleanup completed!"
            ;;
        "logs")
            print_status "Showing container logs..."
            docker-compose logs -f
            ;;
        "shell")
            print_status "Opening shell in application container..."
            docker-compose exec app sh
            ;;
        *)
            echo "Usage: $0 [dev|prod|build|down|clean|logs|shell]"
            echo ""
            echo "Commands:"
            echo "  dev   - Start development environment (default)"
            echo "  prod  - Start production environment"
            echo "  build - Build Docker images"
            echo "  down  - Stop all containers"
            echo "  clean - Stop containers and clean up resources"
            echo "  logs  - Show container logs"
            echo "  shell - Open shell in app container"
            exit 1
            ;;
    esac
}

main "$@"