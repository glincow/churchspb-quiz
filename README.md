# ChurchSPB Quiz Application

A PHP-based quiz application with MySQL database support for managing and taking quizzes.

## Features

- Interactive quiz interface
- Admin panel for quiz management
- MySQL database backend
- Docker-based deployment for easy setup

## Prerequisites

- Docker
- Docker Compose
- Git

## Quick Start

### Local Development with Docker

1. Clone the repository:
```bash
git clone https://github.com/glincow/churchspb-quiz.git
cd churchspb-quiz
```

2. Start the application:
```bash
docker-compose up
```

This will:
- Build the PHP/Apache web container from the Dockerfile
- Start a MySQL 8.0 database container
- Set up all necessary database connections
- Map the application to http://localhost:8080

3. Access the application:
- **Quiz Interface**: http://localhost:8080
- **Admin Panel**: http://localhost:8080/quiz_admin.php

### Database Configuration

The application is pre-configured with the following database settings (set in docker-compose.yml):

- **Database Host**: db
- **Database Name**: quiz_db
- **Database User**: quiz_user
- **Database Password**: quiz_password
- **MySQL Root Password**: root_password

### Stopping the Application

```bash
docker-compose down
```

To also remove the database volume:
```bash
docker-compose down -v
```

## Project Structure

- `index.php` - Main quiz interface
- `quiz_admin.php` - Admin panel for quiz management
- `quiz_ajax.php` - AJAX handlers
- `config.php` - Database configuration
- `Dockerfile` - Docker image definition for PHP/Apache
- `docker-compose.yml` - Multi-container Docker configuration

## GitHub Actions

The repository includes automated workflows that:
- Run PHP syntax checks
- Build Docker images for testing
- Validate the application setup

You can view the workflow runs in the Actions tab.

## Troubleshooting

### Port Already in Use

If port 8080 is already in use, you can modify the port mapping in `docker-compose.yml`:
```yaml
ports:
  - "9090:80"  # Change 8080 to any available port
```

### Database Connection Issues

If you encounter database connection issues:
1. Make sure both containers are running: `docker-compose ps`
2. Check the logs: `docker-compose logs db`
3. Verify the database credentials match those in the docker-compose.yml

### Starting Fresh

To reset the database and start fresh:
```bash
docker-compose down -v
docker-compose up
```

## Contributing

This project is forked from [Zhicha/quiz](https://github.com/Zhicha/quiz).

## License

Please refer to the original repository for license information.
