#!/bin/bash

# Stop and remove the running containers
# cd /home/lbantoine/code/c5-grenier/docker/prod && docker-compose down

# Get the latest code
cd /home/lbantoine/code/c5-grenier && git pull && docker network prune -f && docker image prune -f && docker container prune -f && docker-compose -f /home/lbantoine/code/c5-grenier/docker-compose.server.yml up -d && echo "$(date '+%H:%M:%S   %d/%m/%y') -> Deployment done for commit: $(git log -1 --pretty=%B)" >> /home/lbantoine/code/c5-grenier.log

# Clean docker network
# docker network prune -f

# Clean up older Docker images
# docker image prune -f

# Stop and remove the running containers
# docker container prune -f

# Run the new containers
# docker-compose -f /home/lbantoine/code/c5-grenier/docker/prod/docker-compose.server.yml up -d

# Log the deployment
# echo "$(date '+%H:%M:%S   %d/%m/%y') -> Deployment done for commit: $(git log -1 --pretty=%B)" >> /home/lbantoine/code/c5-grenier.log
