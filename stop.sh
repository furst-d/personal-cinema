#!/bin/bash

docker-compose -f backend/docker-compose.yml down
docker-compose -f frontend/docker-compose.yml down
docker-compose -f admin-frontend/docker-compose.yml down
docker-compose -f cdn-backend/docker-compose.yml down
