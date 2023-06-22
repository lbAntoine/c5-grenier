#!/bin/bash

# Stop and remove the running containers
# Get the latest code
# Clean docker network
# Clean up older Docker images
# Stop and remove the running containers
# Run the new containers
# Log the deployment
cd /home/lbantoine/code/c5-grenier &&
    git pull &&
    # docker network prune -f &&
    # docker image prune -f &&
    # docker container prune -f &&
    docker-compose -f /home/lbantoine/code/c5-grenier/docker-compose.server.yml build &&
    docker-compose -f /home/lbantoine/code/c5-grenier/docker-compose.server.yml up -d &&
    echo "$(date '+%H:%M:%S   %d/%m/%y') -> Deployment done for commit: $(git log -1 --pretty=%B)" >> /home/lbantoine/code/c5-grenier.log
