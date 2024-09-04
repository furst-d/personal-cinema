# Frontend - Personal Cinema

This directory contains the frontend of the **Personal Cinema** project. The frontend is built using **React**, **TypeScript**, and **Vite** for development and bundling. The application is containerized using Docker for both development and production environments.

## Table of Contents
- [Overview](#overview)
- [Technologies](#technologies)
- [Development Setup](#development-setup)
- [Production Setup](#production-setup)
- [Environment Variables](#environment-variables)

## Overview

The frontend is responsible for the user interface and interaction of the **Personal Cinema** platform. It allows users to upload, view, and share audiovisual files, and it integrates with various backend services such as the API and content delivery network (CDN).

## Technologies

- **React**: JavaScript library for building user interfaces.
- **TypeScript**: Type-safe version of JavaScript.
- **Vite**: Fast development build tool and bundler.
- **Material UI**: Component library for building responsive and visually appealing interfaces.
- **Styled-components**: CSS-in-JS library for component styling.
- **Stripe**: Used for payment processing (through `@stripe/react-stripe-js`).
- **Video.js**: HTML5 video player for media playback.

## Development Setup

To set up the development environment using Docker, follow these steps:

1. Clone the repository and navigate to the frontend directory.
2. Create a `.env` file based on the `.env.example` file:
    ```bash
    cp .env.example .env
    ```
   Fill in the required values for the API and CDN URLs, and the Stripe public key.

3. Build the Docker image:
    ```bash
    docker-compose build
    ```

4. Run the development environment using Docker Compose:
    ```bash
    docker-compose up
    ```

This will build the frontend image and start the development server inside a Docker container. The application will be accessible at `http://localhost:3000`, which maps to Vite's default port `5173`.

## Production Setup

The **Personal Cinema** frontend is deployed using **Docker** containers. The production environment is managed via **GitHub Actions**, which automates the deployment process upon pushing to the `main` branch.

### Important Files

- **docker-compose.prod.yml**: Defines how the frontend is deployed in production, specifying the necessary Traefik labels for routing and SSL.
- **Dockerfile.prod**: The production Dockerfile for the frontend. It builds the app, installs a lightweight web server (`serve`), and serves the static files.

### Environment Variables

Before deployment, make sure the required environment variables are set in GitHub Secrets:
- `VITE_API_URL`: The API URL for the backend service.
- `VITE_CDN_URL`: The URL for the CDN to serve media files.
- `VITE_STRIPE_PUBLIC_KEY`: Stripe public key for payment processing.