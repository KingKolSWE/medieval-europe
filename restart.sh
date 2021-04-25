#!/bin/bash
docker kill $(docker container ls | grep -v NAMES | awk '{print $NF}')
docker container prune -f
docker build . -t me:latest
