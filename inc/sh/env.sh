#!/bin/bash

unset PASS
PASS="$1"

unset SUPERDOCK_LOCAL_DOMAIN
SUPERDOCK_LOCAL_DOMAIN="$2"

unset SUPERDOCK_CORE_DIR
SUPERDOCK_CORE_DIR="$3"

unset SUPERDOCK_PROJECT_ID
SUPERDOCK_PROJECT_ID="$4"

unset SUPERDOCK_DOCKER_IP
SUPERDOCK_DOCKER_IP=$(docker-machine ip superdock)

echo $PASS | sudo -S sed -i '' "/#superdock/d" /etc/hosts
echo $PASS | sudo -- sh -c -e "echo '$SUPERDOCK_DOCKER_IP $SUPERDOCK_LOCAL_DOMAIN #superdock' >> /etc/hosts"
echo $PASS | sudo -- sh -c -e "echo '$SUPERDOCK_DOCKER_IP en.$SUPERDOCK_LOCAL_DOMAIN #superdock' >> /etc/hosts"