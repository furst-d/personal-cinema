#!/bin/bash

docker-compose -f backend/docker-compose.yml up -d
docker-compose -f frontend/docker-compose.yml up -d
docker-compose -f admin-frontend/docker-compose.yml up -d
docker-compose -f cdn-backend/docker-compose.yml up -d
