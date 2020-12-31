#!/bin/bash

unset PASS
PASS="$1"

unset SUPERDOCK_LOCAL_DOMAIN
SUPERDOCK_LOCAL_DOMAIN="$2"

unset SUPERDOCK_CORE_DIR
SUPERDOCK_CORE_DIR="$3"

unset SUPERDOCK_PROJECT_ID
SUPERDOCK_PROJECT_ID="$4"

unset SUPERDOCK_PROJECT_DIR
SUPERDOCK_PROJECT_DIR="$5"

echo $PASS | sudo -S security add-trusted-cert -d -r trustRoot -k /Library/Keychains/System.keychain $SUPERDOCK_PROJECT_DIR/superdock/certificate/$SUPERDOCK_LOCAL_DOMAIN/$SUPERDOCK_LOCAL_DOMAIN.pem