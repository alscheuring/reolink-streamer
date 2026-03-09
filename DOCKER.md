# Docker Setup for Reolink Streamer

This document explains how to run the Reolink Streamer application using Docker.

## Prerequisites

- Docker Desktop or Docker Engine installed
- Docker Compose v2.0 or later

## Quick Start

### Development Environment

The easiest way to get started:

```bash
# Make the script executable (first time only)
chmod +x docker-run.sh

# Start development environment
./docker-run.sh dev
```

This will:
- Build the Docker images
- Create the `.env` file if it doesn't exist
- Generate an application key
- Create the SQLite database
- Start all services with hot reloading

The application will be available at: http://localhost:8080

### Production Environment

For production deployment:

```bash
./docker-run.sh prod
```

This runs optimized production containers with:
- OPcache enabled
- Error reporting disabled
- Asset optimization
- Resource limits

## Manual Docker Commands

### Development

```bash
# Build and start development environment
docker compose up --build

# Run in background
docker compose up --build -d

# View logs
docker compose logs -f

# Stop services
docker compose down
```

### Production

```bash
# Build and start production environment
docker compose -f docker-compose.yml -f docker-compose.prod.yml up --build -d

# View logs
docker compose logs -f app

# Stop services
docker compose down
```

## Available Scripts

The `docker-run.sh` script provides several commands:

| Command | Description |
|---------|-------------|
| `dev` | Start development environment (default) |
| `prod` | Start production environment |
| `build` | Build Docker images only |
| `down` | Stop all containers |
| `clean` | Stop containers and remove volumes |
| `logs` | Show container logs |
| `shell` | Open shell in app container |

## Container Architecture

### Services

- **app**: Main Laravel application (Nginx + PHP-FPM)
- **vite**: Development asset server (dev only)

### Ports

- `8080`: Main application
- `5173`: Vite dev server (development only)

### Volumes

#### Development
- Source code is mounted for live editing
- Database and storage persist between runs

#### Production
- Only necessary files are mounted
- Application code is built into the image

## Configuration

### Environment Variables

The application uses these key environment variables in Docker:

```env
APP_URL=http://localhost:8080
DB_CONNECTION=sqlite
DB_DATABASE=/var/www/html/database/database.sqlite
```

### Custom Configuration

1. **Change the port**: Modify `ports` in `docker-compose.yml`
2. **Environment variables**: Edit `.env.docker` template
3. **PHP settings**: Modify files in `docker/php/`
4. **Nginx config**: Edit files in `docker/nginx/`

## Database

The application uses SQLite by default, which simplifies Docker setup:
- Database file: `./database/database.sqlite`
- Automatically created on first run
- Persists between container restarts

### Database Commands

```bash
# Run migrations
docker compose exec app php artisan migrate

# Seed database
docker compose exec app php artisan db:seed

# Access database shell
docker compose exec app sqlite3 database/database.sqlite
```

## Development Workflow

1. **Start development environment**:
   ```bash
   ./docker-run.sh dev
   ```

2. **Edit code**: Files are mounted, so changes are reflected immediately

3. **Run Laravel commands**:
   ```bash
   docker compose exec app php artisan migrate
   docker compose exec app php artisan make:controller CameraController
   ```

4. **View logs**:
   ```bash
   ./docker-run.sh logs
   ```

5. **Access container shell**:
   ```bash
   ./docker-run.sh shell
   ```

## Production Deployment

### Docker Compose

1. Set up your production server
2. Copy the application files
3. Configure environment variables in `.env`
4. Run: `./docker-run.sh prod`

### Container Registry

Build and push to a registry:

```bash
# Build production image
docker build --target app-production -t reolink-streamer:latest .

# Tag for registry
docker tag reolink-streamer:latest your-registry/reolink-streamer:latest

# Push to registry
docker push your-registry/reolink-streamer:latest
```

## Troubleshooting

### Common Issues

1. **Port already in use**: Change port in `docker-compose.yml`
2. **Permission errors**: Check file ownership in `storage/` directory
3. **Database locked**: Stop all containers and restart
4. **Assets not loading**: Ensure Vite service is running in development

### Debugging

```bash
# Check container status
docker compose ps

# View detailed logs
docker compose logs -f app

# Access container shell
docker compose exec app sh

# Check PHP configuration
docker compose exec app php -i

# Test Nginx configuration
docker compose exec app nginx -t
```

### Performance Optimization

1. **Production builds**: Use production Docker target
2. **OPcache**: Enabled in production images
3. **Asset optimization**: Built assets are included in production image
4. **Resource limits**: Set in `docker-compose.prod.yml`

## Security Considerations

1. **Environment files**: Never commit `.env` with sensitive data
2. **File permissions**: Storage directories have correct permissions
3. **Nginx security**: Headers and restrictions configured
4. **Container isolation**: Services run as non-root user where possible

## Maintenance

### Updates

```bash
# Pull latest base images
docker compose pull

# Rebuild with updates
./docker-run.sh build

# Apply Laravel updates
docker compose exec app composer update
docker compose exec app php artisan migrate
```

### Backups

```bash
# Backup SQLite database
cp database/database.sqlite database/database.backup.$(date +%Y%m%d).sqlite

# Backup storage files
tar -czf storage-backup-$(date +%Y%m%d).tar.gz storage/
```