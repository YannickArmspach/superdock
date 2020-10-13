#!/bin/bash
echo $1 | sudo -S sed -i '' "/#superdock/d" /etc/hosts