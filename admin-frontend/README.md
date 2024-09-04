# Admin Frontend - Personal Cinema

This directory contains the admin frontend of the **Personal Cinema** project. The admin frontend is built using **React**, **TypeScript**, and **Vite**, with **react-admin** (from marmelab) for the creation of an admin panel. It allows administrators to manage the backend through a user-friendly interface. The application is containerized using Docker for both development and production environments.

## Table of Contents
- [Overview](#overview)
- [Technologies](#technologies)
- [Development Setup](#development-setup)
- [Production Setup](#production-setup)
- [Environment Variables](#environment-variables)

## Overview

The admin frontend is responsible for managing backend operations in the **Personal Cinema** platform. It allows administrators to perform tasks such as managing users, content, and other backend-related operations through a graphical interface. The admin panel communicates directly with the backend API to perform CRUD operations.

## Technologies

- **React**: JavaScript library for building user interfaces.
- **TypeScript**: Type-safe version of JavaScript.
- **Vite**: Fast development build tool and bundler.
- **react-admin**: Library for building admin panels.
- **Material UI**: Component library for building responsive and visually appealing interfaces.
- **Axios**: Promise-based HTTP client for making API requests.

## Development Setup

To set up the development environment using Docker, follow these steps:

1. Clone the repository and navigate to the admin-frontend directory.
2. Create a `.env` file based on the `.env.example` file:
    ```bash
    cp .env.example .env
    ```
   Fill in the required values for the API URL.

3. Build the Docker image:
    ```bash
    docker-compose build
    ```

4. Run the development environment using Docker Compose:
    ```bash
    docker-compose up
    ```

This will build the admin frontend image and start the development server inside a Docker container. The application will be accessible at `http://localhost:3001`, which maps to Vite's default port `5174`.

## Production Setup

The **Personal Cinema** admin frontend is deployed using **Docker** containers. The production environment is managed via **GitHub Actions**, which automates the deployment process upon pushing to the `main` branch.

### Important Files

- **docker-compose.prod.yml**: Defines how the admin frontend is deployed in production, specifying the necessary Traefik labels for routing and SSL.
- **Dockerfile.prod**: The production Dockerfile for the admin frontend. It builds the app, installs a lightweight web server (`serve`), and serves the static files.

### Environment Variables

Before deployment, make sure the required environment variables are set in GitHub Secrets:
- `VITE_API_URL`: The API URL for the backend service.
