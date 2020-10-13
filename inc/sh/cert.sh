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

openssl genrsa -des3 -out $SUPERDOCK_PROJECT_DIR/superdock/certificate/$SUPERDOCK_LOCAL_DOMAIN/$SUPERDOCK_LOCAL_DOMAIN.key -passout pass:sensiopress 2048
openssl req -x509 -new -nodes -key $SUPERDOCK_PROJECT_DIR/superdock/certificate/$SUPERDOCK_LOCAL_DOMAIN/$SUPERDOCK_LOCAL_DOMAIN.key -passin pass:sensiopress -sha256 -days 1024 -out $SUPERDOCK_PROJECT_DIR/superdock/certificate/$SUPERDOCK_LOCAL_DOMAIN/$SUPERDOCK_LOCAL_DOMAIN.pem -subj "/C=FR/ST=Paris/L=Paris/O=$SUPERDOCK_PROJECT_ID/OU=$SUPERDOCK_PROJECT_ID/CN=$SUPERDOCK_LOCAL_DOMAIN"
openssl req -new -sha256 -nodes -out $SUPERDOCK_PROJECT_DIR/superdock/certificate/$SUPERDOCK_LOCAL_DOMAIN/cert.csr -newkey rsa:2048 -keyout $SUPERDOCK_PROJECT_DIR/superdock/certificate/$SUPERDOCK_LOCAL_DOMAIN/cert.key -config <( printf "[req]\ndefault_bits = 2048\nprompt = no\ndefault_md = sha256\ndistinguished_name = dn\n[dn]\nC=US\nST=RandomState\nL=RandomCity\nO=RandomOrganization\nOU=RandomOrganizationUnit\nemailAddress=hello@$SUPERDOCK_LOCAL_DOMAIN\nCN = $SUPERDOCK_LOCAL_DOMAIN" )
openssl x509 -req -in $SUPERDOCK_PROJECT_DIR/superdock/certificate/$SUPERDOCK_LOCAL_DOMAIN/cert.csr -CA $SUPERDOCK_PROJECT_DIR/superdock/certificate/$SUPERDOCK_LOCAL_DOMAIN/$SUPERDOCK_LOCAL_DOMAIN.pem -CAkey $SUPERDOCK_PROJECT_DIR/superdock/certificate/$SUPERDOCK_LOCAL_DOMAIN/$SUPERDOCK_LOCAL_DOMAIN.key -CAcreateserial -out $SUPERDOCK_PROJECT_DIR/superdock/certificate/$SUPERDOCK_LOCAL_DOMAIN/cert.crt -days 365 -sha256 -passin pass:sensiopress -extfile <( printf "authorityKeyIdentifier=keyid,issuer\nbasicConstraints=CA:FALSE\nkeyUsage = digitalSignature, nonRepudiation, keyEncipherment, dataEncipherment\nsubjectAltName = @alt_names\n\n[alt_names]\nDNS.1 = $SUPERDOCK_LOCAL_DOMAIN" )
openssl x509 -req -in $SUPERDOCK_PROJECT_DIR/superdock/certificate/$SUPERDOCK_LOCAL_DOMAIN/cert.csr -CA $SUPERDOCK_PROJECT_DIR/superdock/certificate/$SUPERDOCK_LOCAL_DOMAIN/$SUPERDOCK_LOCAL_DOMAIN.pem -CAkey $SUPERDOCK_PROJECT_DIR/superdock/certificate/$SUPERDOCK_LOCAL_DOMAIN/$SUPERDOCK_LOCAL_DOMAIN.key -CAcreateserial -out $SUPERDOCK_PROJECT_DIR/superdock/certificate/$SUPERDOCK_LOCAL_DOMAIN/cert.en.crt -days 365 -sha256 -passin pass:sensiopress -extfile <( printf "authorityKeyIdentifier=keyid,issuer\nbasicConstraints=CA:FALSE\nkeyUsage = digitalSignature, nonRepudiation, keyEncipherment, dataEncipherment\nsubjectAltName = @alt_names\n\n[alt_names]\nDNS.1 = en.$SUPERDOCK_LOCAL_DOMAIN" )

echo $PASS | sudo -S security add-trusted-cert -d -r trustRoot -k /Library/Keychains/System.keychain $SUPERDOCK_PROJECT_DIR/superdock/certificate/$SUPERDOCK_LOCAL_DOMAIN/$SUPERDOCK_LOCAL_DOMAIN.pem