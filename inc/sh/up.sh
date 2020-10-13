#!/bin/bash

unset PASS
PASS="$1"

unset SUPERDOCK_LOCAL_DOMAIN
SUPERDOCK_LOCAL_DOMAIN="$2"

unset SUPERDOCK_CORE_DIR
SUPERDOCK_CORE_DIR="$3"

unset SUPERDOCK_PROJECT_ID
SUPERDOCK_PROJECT_ID="$4"

echo $PASS | sudo -S sed -i '' "/#superdock/d" /etc/hosts
echo $PASS | sudo -- sh -c -e "echo '127.0.0.1 $SUPERDOCK_LOCAL_DOMAIN #superdock' >> /etc/hosts"
echo $PASS | sudo -- sh -c -e "echo '127.0.0.1 en.$SUPERDOCK_LOCAL_DOMAIN #superdock' >> /etc/hosts"