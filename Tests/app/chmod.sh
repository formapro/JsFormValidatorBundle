#!/bin/bash

HTTPDUSER=`ps aux | grep -E '[a]pache|[h]ttpd|[_]www|[w]ww-data|[n]ginx' | grep -v root | head -1 | cut -d\  -f1`
ROOT=$(pwd)

fix() {
    mkdir -p ${ROOT}/$1
    touch "${ROOT}/$1/.gitkeep"
    sudo setfacl -R -m u:${HTTPDUSER}:rwX -m u:${USER}:rwX ${ROOT}/$1
    sudo setfacl -dR -m u:${HTTPDUSER}:rwX -m u:${USER}:rwX ${ROOT}/$1
}

fix cache
fix logs
chmod 0777 ./../../Resources/public/js/fp_js_validator.js