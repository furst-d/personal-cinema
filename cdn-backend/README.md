# CDN Backend - Personal Cinema

This directory contains the CDN backend for the **Personal Cinema** project. It is built using **Node.js**, **Express**, and **Sequelize**. The backend handles video file processing, storage using **Minio**, and task management using **Bull** with **Redis** as a queue system. The database layer is managed by **PostgreSQL**. The application is containerized using Docker for both development and production environments.

## Table of Contents
- [Overview](#overview)
- [Technologies](#technologies)
- [Development Setup](#development-setup)
- [Production Setup](#production-setup)
- [Environment Variables](#environment-variables)
- [Video Upload and Processing Flow](#video-upload-and-processing-flow)
- [API Endpoints](#api-endpoints)
    - [Project Routes](#project-routes)
    - [Upload Routes](#upload-routes)
    - [Video Routes](#video-routes)
- [Callbacks](#callbacks)
- [Running Tests](#running-tests)

## Overview

The CDN backend is responsible for handling file uploads, video processing, and storage. It integrates with **Minio** for storing files and uses **Bull** and **Redis** for managing video processing tasks. The backend API allows users to upload and manage media files. **PostgreSQL** is used as the primary database to store metadata for projects, callbacks, and settings.

### Key Features
1. **File Upload**: Files are first stored locally, and a response is sent to the user immediately after upload.
2. **Minio Integration**: Files are uploaded to **Minio** storage asynchronously after the local storage step.
3. **Video Processing**: After the upload, metadata is retrieved, HLS conversions are generated for multiple resolutions, and 20 preview images are created.
4. **Secured Access**: Files stored in Minio are accessed using signed URLs, providing secure access to media files.
5. **Minio Console**: Minio offers a graphical interface for managing stored files.

## Technologies

- **Node.js**: JavaScript runtime environment.
- **Express**: Web framework for building APIs.
- **Sequelize**: ORM for database management.
- **PostgreSQL**: Relational database used for storing metadata.
- **Bull**: Queue system for task management.
- **Redis**: In-memory data store used as a task queue.
- **Minio**: Object storage service compatible with AWS S3.
- **FFmpeg**: Multimedia framework used for video processing.

## Development Setup

To set up the development environment using Docker, follow these steps:

1. Clone the repository and navigate to the `cdn-backend` directory.
2. Create a `.env` file based on the `.env.example` file:
    ```bash
    cp .env.example .env
    ```
   Fill in the required values such as Minio credentials, PostgreSQL, and Redis details.

3. Add a record to your `hosts` file to ensure proper routing to Minio:

    - **Windows**: Edit the `C:\Windows\System32\drivers\etc\hosts` file.
    - **Linux**: Edit the `/etc/hosts` file.

   Add the following line to the file:

    ```plaintext
    127.0.0.1    minio
    ```
   
4. Build the Docker image:
    ```bash
    docker-compose build
    ```

5. Run the development environment using Docker Compose:
    ```bash
    docker-compose up
    ```

This will build the CDN backend image and start the development server inside a Docker container. The application will be accessible at `http://localhost:4000`.

5. **Important**: After starting the application for the first time, you need to execute an SQL script to insert default values into the database. Run the following SQL script on your PostgreSQL database:
    ```bash
    scripts/init-db.sql
    ```
   This script includes default settings, project data, and callback URLs. Please modify the script according to your environment and requirements before running it.

## Production Setup

The **Personal Cinema** CDN backend is deployed using **Docker** containers. The production environment is managed via **GitHub Actions**, which automates the deployment process upon pushing to the `main` branch.

### Important Files

- **docker-compose.prod.yml**: Defines how the CDN backend is deployed in production, specifying the necessary Traefik labels for routing and SSL.
- **Dockerfile.prod**: The production Dockerfile for the CDN backend. It installs the necessary dependencies, builds the TypeScript code, and serves the app.

### Environment Variables

Before deployment, make sure the required environment variables are set in GitHub Secrets:
- `NODE_ENV`: The environment mode, typically `production`.
- `PORT`: The port on which the server runs.
- `MINIO_*`: Credentials and configuration for connecting to Minio.
- `POSTGRES_*`: Database credentials for the PostgreSQL database.
- `REDIS_*`: Redis credentials for task queue management.

## Video Upload and Processing Flow

1. **Initial Upload**: The file is uploaded locally, and the API responds immediately.
2. **Minio Storage**: The file is asynchronously uploaded to Minio storage.
3. **Video Processing**:
    - Video metadata is extracted.
    - HLS video streams are generated for multiple resolutions.
    - 20 preview images are created.
4. **Secured Access**: Files in Minio are accessed through signed URLs, ensuring secure file access.
5. **Callbacks**: During video processing, the system sends periodic notifications via **callbacks**:
    - **[Notification callbacks](#callbacks)** provide updates on the current processing status.
    - **[Thumbnail callbacks](#callbacks)** notify when preview images (thumbnails) are generated.

The callback system ensures that the client is kept up to date with the progress of video processing and is notified once the media is ready for viewing.

## API Endpoints

### Project Routes

| Method | Endpoint    | Description                | Authentication |
|--------|-------------|----------------------------|----------------|
| POST   | /projects    | Create a new project       | Yes            |
| PUT    | /projects/:id | Update an existing project | Yes            |

- **Create Project** (`POST /projects`): Creates a new project in the system. This route is secured and requires authentication.
- **Update Project** (`PUT /projects/:id`): Updates details of an existing project based on its ID. This route is secured and requires authentication.

### Upload Routes

| Method | Endpoint  | Description       | Authentication |
|--------|-----------|-------------------|----------------|
| POST   | /upload   | Upload a video    | Yes (Signature Verification) |

- **Upload Video** (`POST /upload`): Uploads a video file to the server. The video is first stored locally, and a response is sent to the client. Then, the video is uploaded to Minio, and video processing (HLS, thumbnails) is handled asynchronously. This route requires signature verification for authentication.

### Video Routes

| Method | Endpoint                 | Description                        | Authentication |
|--------|--------------------------|------------------------------------|----------------|
| GET    | /videos/:id               | Get video details                  | Yes            |
| GET    | /videos/:id/file.m3u8     | Get HLS video URL for streaming    | Yes            |
| GET    | /videos/:id/thumbs        | Get signed URLs for thumbnails     | Yes            |
| GET    | /videos/:id/thumbs/:thumbNumber | Get a specific thumbnail        | No             |
| GET    | /videos/:id/download      | Get signed download link for video | No             |
| DELETE | /videos                   | Batch delete videos                | Yes            |

- **Get Video Details** (`GET /videos/:id`): Retrieves the metadata and details for a specific video, including status and processed data (HLS, thumbnails). Requires authentication.
- **Get HLS Video URL** (`GET /videos/:id/file.m3u8`): Returns a signed manifest file (.m3u8) with HLS video stream URLs. Requires authentication.
- **Get Thumbnails** (`GET /videos/:id/thumbs`): Returns signed URLs for the generated video thumbnails. Requires authentication.
- **Get Specific Thumbnail** (`GET /videos/:id/thumbs/:thumbNumber`): Fetches a specific thumbnail image.
- **Get Video Download Link** (`GET /videos/:id/download`): Returns a signed URL for downloading the original video.
- **Batch Delete Videos** (`DELETE /videos`): Deletes a batch of videos by marking them for deletion. Requires authentication.

## Callbacks

During the video processing lifecycle, the system sends out notifications via **callbacks**:

- **Notification Callback**: Sent at various stages of the video processing (e.g., when the video upload is complete, HLS segments are created, or thumbnails are generated).
- **Thumbnail Callback**: Sent when thumbnails are ready, notifying the client about the availability of preview images.

The callback system ensures that the client is updated with the video processing progress.

## Running Tests

The CDN backend includes a set of **Jest** tests to verify the functionality of the application. These tests are run inside the **CDN Backend Docker container**.

To run the tests, use the following command inside the running Docker container:

```bash
docker exec -it cdn-backend-cdn-api-1 npm run test
```