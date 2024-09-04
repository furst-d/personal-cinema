# Personal Cinema

<img src="./docs/logo_red.svg" alt="Personal Cinema Logo" width="300" />

**Personal Cinema** is a private cloud-based platform for managing and sharing audiovisual files. It allows users to store, process, and share videos either privately with other users or publicly via generated access links. The project is structured into multiple services: Frontend, Admin Frontend, CDN Backend, and Backend.

## Table of Contents
- [Project Overview](#project-overview)
- [Applications](#applications)
    - [Frontend](#frontend)
    - [Admin Frontend](#admin-frontend)
    - [CDN Backend](#cdn-backend)
    - [Backend](#backend)
- [Getting Started](#getting-started)
- [Deployment](#deployment)

## Project Overview

The **Personal Cinema** platform provides a way to upload, process, and share video content. It is divided into four main applications, each responsible for a specific role in the system:

1. **Frontend**: The user-facing interface where users can upload and share videos.
2. **Admin Frontend**: The admin panel for managing backend operations.
3. **CDN Backend**: Handles video processing and serves content via a secure object storage system.
4. **Backend**: The core API that handles user management, authentication, and other essential server-side logic.

## Applications

### Frontend
The **Frontend** is the main user-facing interface, built using **React**, **TypeScript**, and **Vite**. It allows users to upload, view, and share audiovisual files.

- **[Frontend Documentation](./frontend/README.md)**

### Admin Frontend
The **Admin Frontend** is an administrative panel used for managing backend services. It is built using **React** and **Marmelab's React Admin** library.

- **[Admin Frontend Documentation](./admin-frontend/README.md)**

### CDN Backend
The **CDN Backend** is responsible for video processing, storage using **Minio**, and task management with **Bull** and **Redis**. It provides secure storage and access to video content.

- **[CDN Backend Documentation](./cdn-backend/README.md)**

### Backend
The **Backend** is the core API built using **Symfony**. It handles user management, authentication using **JWT**, and other server-side logic. It also exposes the API documented with **Swagger**.

- **[Backend Documentation](./backend/README.md)**

## Getting Started

To set up and run the **Personal Cinema** project, each application must be configured and started individually.

1. Clone the repository:
    ```bash
    git clone https://github.com/your-repo/personal-cinema.git
    cd personal-cinema
    ```

2. Follow the setup instructions in the README file of each application:
    - **[Frontend Setup](./frontend/README.md)**
    - **[Admin Frontend Setup](./admin-frontend/README.md)**
    - **[CDN Backend Setup](./cdn-backend/README.md)**
    - **[Backend Setup](./backend/README.md)**

Each service has its own configuration, environment variables, and setup steps. Ensure that all services are properly configured before running them.

## Deployment

The **Personal Cinema** project is deployed using **GitHub Actions** and Docker containers. Upon each push to the `main` branch, the services are automatically built, tested, and deployed to the server. The deployment process involves:

1. **Building Docker Images**: Each service (Frontend, Admin Frontend, CDN Backend, and Backend) has a Docker image that is built and pushed to a Docker registry.

2. **Environment Setup**: During the deployment process, `.env` files for each service are automatically generated based on the `.env.example` files. Values from **GitHub Secrets** are used to replace placeholders in these `.env` files to ensure the correct configuration for each service.

3. **GitHub Actions Workflow**: The deployment pipeline is managed via **GitHub Actions**, which automates the process of building, testing, and pushing Docker images. Each push to the `main` branch triggers the deployment workflow.

4. **Running Tests**: As part of the deployment process, unit and integration tests are automatically executed. If any tests fail, the deployment process is halted, ensuring only stable code is deployed.

5. **Database Migrations**: After a successful test run, database migrations are automatically applied to ensure the database schema is up to date with the codebase.

6. **Traefik Integration**: **Traefik** is used as a reverse proxy to route traffic to the correct services. The services are exposed via Traefik with SSL certificates managed using Let's Encrypt.

### Steps for Deployment:

1. Ensure that all necessary **environment variables** are set in the GitHub repository's secrets for each service (Frontend, Admin Frontend, CDN Backend, and Backend).

2. Push changes to the `main` branch:
    ```bash
    git push origin main
    ```

3. The **GitHub Actions** workflow will automatically:
    - Generate the `.env` files by replacing placeholders in the `.env.example` files with values from GitHub Secrets.
    - Build Docker images for each service.
    - Run unit and integration tests. If the tests fail, the deployment will stop.
    - Apply any pending database migrations.
    - Push the updated Docker images to a Docker registry.
    - Deploy the updated services to the server.

4. **Traefik** will manage the routing of requests and SSL certificates for each service, ensuring secure access to the application.

For more details on the deployment process, refer to the individual application documentation:
- **[Frontend Deployment](./frontend/README.md)**
- **[Admin Frontend Deployment](./admin-frontend/README.md)**
- **[CDN Backend Deployment](./cdn-backend/README.md)**
- **[Backend Deployment](./backend/README.md)**
