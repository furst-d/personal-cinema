# Backend - Personal Cinema

This directory contains the backend of the **Personal Cinema** project. It is built using **Symfony** as the web framework and **Nginx** as the web server. The backend handles API requests, authentication, and other server-side logic. It uses **PostgreSQL** as the primary database and supports **JWT**-based authentication. The application is containerized using Docker for both development and production environments.

## Table of Contents
- [Overview](#overview)
- [Technologies](#technologies)
- [Development Setup](#development-setup)
- [Production Setup](#production-setup)
- [Environment Variables](#environment-variables)
- [Authentication and Authorization](#authentication-and-authorization)
- [Swagger API Documentation](#swagger-api-documentation)
- [Running Tests](#running-tests)

## Overview

The backend API serves as the core of the **Personal Cinema** project, handling all API requests related to user management, video processing, and content delivery. It uses **Symfony** for routing and controller management, and **Nginx** serves as the web server. The backend communicates with **PostgreSQL** for data storage and uses **JWT** tokens for user authentication and authorization.

## Technologies

- **Symfony**: A PHP framework used for building web applications.
- **Nginx**: A web server used for handling HTTP requests and serving the Symfony application.
- **PostgreSQL**: A relational database for storing application data.
- **JWT (JSON Web Tokens)**: Used for secure authentication and authorization.
- **Swagger**: Used for documenting and interacting with the API.
- **Docker**: Containerization platform for both development and production environments.

## Development Setup

To set up the development environment using Docker, follow these steps:

1. Clone the repository and navigate to the backend directory.
2. Create a `.env` file based on the `.env.example` file in the root and `symfony/.env` files:
    ```bash
    cp .env.example .env
    cp symfony/.env.example symfony/.env
    ```
   Fill in the required values, such as PostgreSQL credentials, JWT keys, and API tokens.

3. Build the Docker images:
    ```bash
    docker-compose build
    ```

4. Run the development environment using Docker Compose:
    ```bash
    docker-compose up
    ```

This will start the backend server along with Nginx and PostgreSQL. The application will be accessible at `http://localhost:8080`.

## Production Setup

The **Personal Cinema** backend is deployed using **Docker** containers. The production environment is managed via **GitHub Actions**, which automates the deployment process upon pushing to the `main` branch.

### Important Files

- **docker-compose.prod.yml**: Defines how the backend is deployed in production, specifying the necessary Traefik labels for routing and SSL.
- **Dockerfile** (PHP-FPM): Configures the PHP environment and installs necessary dependencies.
- **Dockerfile** (Nginx): Configures the Nginx server to serve the Symfony application.

## Environment Variables

The project relies on environment variables that are loaded from the `.env` file in the root and the `symfony/.env` file for Symfony-specific configuration. Here are the key variables:

### Root `.env`:

- `APP_ENV`: Environment mode, typically `prod` for production.
- `POSTGRES_DB`: Name of the PostgreSQL database.
- `POSTGRES_USER`: PostgreSQL username.
- `POSTGRES_PASSWORD`: PostgreSQL password.
- `DATABASE_URL`: URL for connecting to the PostgreSQL database.

### Symfony `.env`:

- `APP_SECRET`: Secret key for Symfony.
- `DATABASE_URL`: URL for the PostgreSQL database connection.
- `JWT_SECRET_KEY`: Path to the private key for JWT.
- `JWT_PUBLIC_KEY`: Path to the public key for JWT.
- `JWT_PASSPHRASE`: Passphrase for JWT key.
- `MAILERSEND_API_KEY`: API key for MailerSend.
- `CDN_*`: API details for communicating with the CDN service.
- `BACKEND_URL`: URL for accessing the backend.
- `FRONTEND_URL`: URL for the frontend.
- `STRIPE_SECRET_KEY`: Stripe API key for payment processing.
- `API_DOC_PASSWORD`: Password for accessing the Swagger API documentation.

## Authentication and Authorization

The backend uses **JWT tokens** for authentication and authorization. The JWT tokens are generated and verified using **LexikJWTAuthenticationBundle**. This allows secure communication between the client and server.

### JWT Setup

- Private and public keys are stored in the `symfony/config/jwt/` directory.
- JWT tokens are used to authenticate all protected routes in the API.

To generate a new JWT token, a user must provide valid credentials. The token is then used in the `Authorization` header in subsequent requests.

Example of a request with a JWT token:
```http
GET /api/protected-resource
Authorization: Bearer <your-jwt-token>
```

## Swagger API Documentation

The backend API is documented using **Swagger** via the **NelmioApiDocBundle**. The documentation is accessible at `/doc` and the JSON version at `/doc.json`.

- **Access URL**: `http://localhost:8080/doc`
- **Basic Authentication**: The API documentation is secured using basic HTTP authentication. To access it, use the password stored in the `.env` file under `API_DOC_PASSWORD`.

Example login to access API docs:
```bash
Username: user
Password: <API_DOC_PASSWORD>
```


## Running Tests

The backend includes a set of **PHPUnit** tests to verify the functionality of the application. These tests are run inside the **PHP-FPM Docker container**.

To run the tests, use the following command inside the running Docker container:

```bash
docker exec -it backend-php-fpm-1 php /var/www/vendor/bin/phpunit --configuration /var/www/phpunit.xml.dist
```