#!/bin/bash

echo "Build services"
USERNAME=$(whoami)
docker-compose build \
  --no-cache \
  --build-arg USERNAME=$USERNAME \
  --build-arg USERID=$(id -u) \
  --build-arg GROUPID=$(id -g $USERNAME)

echo "Create and start containers"
docker-compose up -d --force-recreate

echo "Setting up the application"
docker-compose exec app sh /app/setup_docker.sh
